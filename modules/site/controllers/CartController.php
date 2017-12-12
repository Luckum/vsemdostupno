<?php

namespace app\modules\site\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\base\Exception;
use app\models\Cart;
use app\models\Order;
use app\models\OrderHasProduct;
use app\models\Page;
use app\models\Partner;
use app\models\User;
use app\models\Member;
use app\modules\site\models\OrderForm;
use app\models\Email;
use app\models\Account;
use app\models\OrderStatus;
use app\models\StockHead;
use app\models\StockBody;
use yii\db\Query;
use app\models\ProviderStock;
use app\models\Provider;
use app\models\UnitContibution;
use app\models\ProviderHasProduct;

class CartController extends BaseController
{
    public function behaviors()
    {
        $enableCart = false;
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->identity->role == User::ROLE_PROVIDER) {
                $member = Member::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
                if ($member) {
                    $enableCart = true;
                }
            }
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['order'],
                        'matchCallback' => function ($rule, $action) {
                            if (Cart::isEmpty()) {
                                throw new ForbiddenHttpException('Действие не разрешено.');
                            }

                            return true;
                        },
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($enableCart) {
                            if (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->role, [User::ROLE_ADMIN, User::ROLE_PROVIDER]) && !$enableCart) {
                                throw new ForbiddenHttpException('Действие не разрешено.');
                            }

                            return true;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Cart();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionOrder()
    {
        if (!Yii::$app->user->isGuest) {
            $cart = new Cart();
            $deposit = Yii::$app->user->identity->entity->deposit;

            if ($cart->total > $deposit->total) {
                Yii::$app->session->setFlash('message', 'Недостаточно средств на счете для совершения покупки!');

                return $this->redirect('/cart');
            }
        }

        $paid_for_provider=0;
        $model = new OrderForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $order = new Order();

                $order->email = $model->email;
                $order->phone = '+' . preg_replace('/\D+/', '', $model->phone);
                $order->firstname = $model->firstname;
                $order->lastname = $model->lastname;
                $order->patronymic = $model->patronymic;
                $order->address = $model->address;
                $order->comment = $model->comment;
                if (!Yii::$app->user->isGuest) {
                    $order->paid_total = $cart->total;
                }

                if ($model->partner) {
                    $partner = Partner::findOne($model->partner);

                    if ($partner) {
                        $order->partner_id = $partner->id;
                        $order->partner_name = $partner->name;
                        $order->city_id = $partner->city->id;
                        $order->city_name = $partner->city->name;
                    }
                } elseif (!Yii::$app->user->isGuest) {
                    if (in_array(Yii::$app->user->identity->role, [User::ROLE_PARTNER])) {
                        $partner = Yii::$app->user->identity->entity->partner;
                        $order->city_id = $partner->city->id;
                        $order->city_name = $partner->city->name;
                    }
                }

                if (!Yii::$app->user->isGuest) {
                    $entity = Yii::$app->user->identity->entity;
                    $order->user_id = $entity->id;
                    $order->role = $entity->role;
                    
                    if ($entity->role == User::ROLE_PROVIDER) {
                        $member = Member::find()->where(['user_id' => $entity->id])->one();
                        if ($member) {
                            $order->role = User::ROLE_MEMBER;
                        }
                    }
                }

                $cart = new Cart();
                $order->total = $cart->total;

                $orderStatus = OrderStatus::findOne(['type' => OrderStatus::STATUS_NEW]);
                $order->order_status_id = $orderStatus->id;

                if (!($cart->products && $order->save())) {
                    throw new Exception('Ошибка сохранения заказа!');
                }

                foreach ($cart->products as $product) {
                    if ($product->orderDate && (strtotime($product->orderDate) + strtotime('1 day', 0)) < time()) {
                        throw new Exception('Товар нельзя заказать!');
                    }

                    if (isset($product->inventory)) {
                        $product->scenario = 'order_product';
                        $product->inventory -= $product->quantity;

                        if (!$product->save()) {
                            throw new Exception('Ошибка обновления количества товара в магазине!');
                        }
                    }

                    $orderHasProduct = new OrderHasProduct();

                    $orderHasProduct->order_id = $order->id;
                    $orderHasProduct->product_id = $product->id;
                    $orderHasProduct->name = $product->name;
                    $orderHasProduct->orderDate = $product->orderDate;
                    $orderHasProduct->purchaseDate = $product->purchaseDate;
                    $orderHasProduct->price = $product->calculatedPrice;
                    $orderHasProduct->purchase_price = $product->purchase_price;
                    $orderHasProduct->storage_price = $product->storage_price;
                    $orderHasProduct->invite_price = $product->calculatedInvitePrice;
                    $orderHasProduct->fraternity_price = $product->calculatedFraternityPrice;
                    if (!$order->user) {
                        $orderHasProduct->group_price = $product->price - $product->partner_price;
                    } elseif (!in_array($order->user->role, [User::ROLE_PARTNER])) {
                        $orderHasProduct->group_price = $product->calculatedGroupPrice;
                    } else {
                        $orderHasProduct->group_price = 0;
                    }
                    $orderHasProduct->quantity = $product->quantity;
                    $orderHasProduct->total = $product->calculatedTotalPrice;
                    
                    $provider = ProviderHasProduct::find()->where(['product_id' => $product->id])->one();
                    $provider_id = $provider ? $provider->provider_id : 0;

                    if ($provider_id != 0) {
                        $orderHasProduct->provider_id = $provider_id;
                        $stock_provider = ProviderStock::getCurrentStock($product->id, $provider_id);
                        
                        $provider_model = Provider::findOne(['id' => $provider_id]);
                        $provider_account = Account::findOne(['user_id' => $provider_model->user_id]);

                        if ($stock_provider) {
                            if ($stock_provider->reaminder_rent >= $orderHasProduct->quantity) {
                                $stock_provider->reaminder_rent -= $orderHasProduct->quantity;
                                $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                $paid_for_provider = $orderHasProduct->quantity * $body->summ;
                                $stock_provider->summ_on_deposit += $paid_for_provider;
                                $stock_provider->save();
                                
                            } else {
                                $rest = $orderHasProduct->quantity - $stock_provider->reaminder_rent;
                                $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                $stock_provider->summ_on_deposit += $stock_provider->reaminder_rent * $body->summ;
                                $stock_provider->reaminder_rent = 0;
                                $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                $stock_provider->save();
                                
                                while ($rest > 0) {
                                    $stock_provider = ProviderStock::getCurrentStock($product->id, $provider_id);
                                    
                                    if ($stock_provider->reaminder_rent >= $rest) {
                                        $stock_provider->reaminder_rent -= $rest;
                                        $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                        $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                        $paid_for_provider = $rest * $body->summ;
                                        $stock_provider->summ_on_deposit += $paid_for_provider;
                                        $stock_provider->save();
                                        $rest = 0;
                                    } else {
                                        $rest -= $stock_provider->reaminder_rent;
                                        $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                        $stock_provider->summ_on_deposit += $stock_provider->reaminder_rent * $body->summ;
                                        $stock_provider->reaminder_rent = 0;
                                        $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                        $stock_provider->save();
                                    }
                                }
                            }
                            $paid_for_provider = $orderHasProduct->quantity * $body->summ;
                        }

                        
                        if ($body->deposit == '1') {
                            if (!Account::swap($deposit, $provider_account, $paid_for_provider, 'Перевод пая на счёт')) {
                                throw new Exception('Ошибка модификации счета пользователя!');
                            }
                        } else {
                            $paid_for_provider = 0;
                        }
                        $unitContibution = new UnitContibution();
                        $unitContibution->order_id=$orderHasProduct->order_id;
                        $unitContibution->provider_stock_id=$stock_provider->id;
                        $unitContibution->on_deposit=$stock_provider->total_sum-$stock_provider->summ_reminder;

                        $unitContibution->save();
                    }
                    if (!$orderHasProduct->save()) {
                        throw new Exception('Ошибка сохранения товара в заказе!');
                    }
                }


                if ($order->paid_total > 0) {
                    if ($order->paid_total == $order->total) {
                        $message = sprintf('Списано по заказу №%s.', $order->id);
                    } else {
                        $message = sprintf('Частичная списано по заказу №%s.', $order->id);
                    }

                    if (!Account::swap($deposit, null, $order->paid_total-$paid_for_provider, $message)) {
                       throw new Exception('Ошибка модификации счета пользователя!');
                    }
                    if ($entity->role == User::ROLE_PROVIDER) {
                        ProviderStock::setStockSum($entity->id, $order->paid_total-$paid_for_provider);
                    }

                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();


                Yii::$app->session->setFlash('cart-checkout', [
                    'name' => 'cart-checkout-fail',
                ]);

                return $this->redirect('/cart/checkout');
            }

            $cart->clear();

            Email::send('order-customer', Yii::$app->params['adminEmail'], [
                'id' => $order->id,
                'information' => $order->htmlEmailFormattedInformation,
            ]);

            if ($order->partner) {
                Email::send('order-partner', $order->partner->email, [
                    'id' => $order->id,
                    'information' => $order->htmlEmailFormattedInformation,
                ]);
            }

            Email::send('order-customer', $order->email, [
                'id' => $order->id,
                'information' => $order->htmlEmailFormattedInformation,
            ]);

            Yii::$app->session->setFlash('cart-checkout', [
                'name' => 'cart-checkout-success',
                'order' => $order,
            ]);

            return $this->redirect('/cart/checkout');
        } else {
            return $this->render('order', [
                'model' => $model,
            ]);
        }
    }

    public function actionCheckout()
    {
        $data = Yii::$app->session->getFlash('cart-checkout');

        if (!$data) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $model = Page::findOne(['slug' => $data['name']]);
        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        if (isset($data['order'])) {
            $attributes = array_keys($data['order']->attributeLabels());
            $patterns = [];
            $replacements = [];

            foreach ($attributes as $attribute) {
                $patterns[] = '/{{%' . $attribute . '}}/';
                $replacements[] = $data['order']->$attribute;
            }

            $model->title = preg_replace($patterns, $replacements, $model->title);
            $model->content = preg_replace($patterns, $replacements, $model->content);
        }

        return $this->render('checkout', [
            'model' => $model,
        ]);
    }
}
