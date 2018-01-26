<?php

namespace app\modules\site\controllers\profile\partner;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\modules\site\controllers\BaseController;
use app\modules\site\models\profile\partner\MemberForm;
use app\modules\site\models\profile\partner\OrderForm;
use app\modules\site\models\profile\partner\AccountForm;
use app\models\User;
use app\models\Member;
use app\models\Order;
use app\models\OrderStatus;
use app\models\OrderHasProduct;
use app\models\Product;
use app\models\Account;
use app\models\AccountLog;
use app\models\Email;
use app\helpers\Html;

class MemberController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'order',
                            'order-create',
                            'account',
                            'update',
                        ],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (in_array(Yii::$app->user->identity->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])) {
                                $action->controller->redirect('/admin')->send();
                                exit();
                            }

                            if (!in_array(Yii::$app->user->identity->role, [User::ROLE_PARTNER])) {
                                throw new ForbiddenHttpException('Действие не разрешено.');
                            }

                            if (Yii::$app->user->identity->entity->disabled) {
                                $action->controller->redirect('/profile/logout')->send();
                                exit();
                            }

                            return true;
                        },
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Member::find()->where('partner_id = :partner_id', [':partner_id' => $this->identity->entity->partner->id]),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrder()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where('partner_id = :partner_id', [':partner_id' => $this->identity->entity->partner->id]),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        return $this->render('order', [
            'title' => 'Заказы моих участников',
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrderCreate()
    {
        $model = new OrderForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne($model->user_id);

            if ($user->member->partner_id != $this->identity->entity->partner->id) {
                throw new ForbiddenHttpException('Действие не разрешено.');
            }

            $productList = Json::decode($model->product_list);
            $productList = ArrayHelper::map($productList, 'id', 'quantity');

            $products = Product::find()
                ->where(['IN', 'id', array_keys($productList)])
                ->all();
            foreach ($products as $index => $product) {
                $products[$index]->quantity = $productList[$product->id];
            }
            $total = 0;
            foreach ($products as $product) {
                $total += $product->quantity * $product->getPriceByRole($user->role);
            }

            if ($total > $user->deposit->total) {
                throw new ForbiddenHttpException('Недостаточно средств на счете для совершения покупки!');
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $order = new Order();

                $order->email = $user->email;
                $order->phone = $user->phone;
                $order->firstname = $user->firstname;
                $order->lastname = $user->lastname;
                $order->patronymic = $user->patronymic;
                $order->comment = 'Заказа сделан через панель партнера.';
                $order->paid_total = $total;
                $order->total = $total;

                $partner = Yii::$app->user->identity->entity->partner;
                $order->partner_id = $partner->id;
                $order->partner_name = $partner->name;
                $order->city_id = $partner->city->id;
                $order->city_name = $partner->city->name;

                $order->user_id = $user->id;
                $order->role = $user->role;

                $orderStatus = OrderStatus::findOne(['type' => OrderStatus::STATUS_NEW]);
                $order->order_status_id = $orderStatus->id;

                if (!$order->save()) {
                    throw new Exception('Ошибка сохранения заказа!');
                }

                foreach ($products as $product) {
                    if (!$product->inventory && $product->orderDate && (strtotime($product->orderDate) + strtotime('1 day', 0)) < time()) {
                        throw new Exception('"' . $product->name . '" нельзя заказать!');
                    }

                    if (isset($product->inventory)) {
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
                    $orderHasProduct->price = $product->getPriceByRole($user->role);
                    $orderHasProduct->purchase_price = $product->purchase_price;
                    $orderHasProduct->storage_price = $product->storage_price;
                    $orderHasProduct->invite_price = $product->calculatedInvitePrice;
                    $orderHasProduct->fraternity_price = $product->calculatedFraternityPrice;
                    if (!$user) {
                        $orderHasProduct->group_price = $product->price - $product->partner_price;
                    } elseif (!in_array($user->role, [User::ROLE_PARTNER])) {
                        $orderHasProduct->group_price = $product->calculatedGroupPrice;
                    } else {
                        $orderHasProduct->group_price = 0;
                    }
                    $orderHasProduct->quantity = $product->quantity;
                    $orderHasProduct->total = $product->quantity * $product->getPriceByRole($user->role);

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

                    if (!Account::swap($user->deposit, null, $order->paid_total, $message)) {
                        throw new Exception('Ошибка модификации счета пользователя!');
                    }
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();

                throw new ForbiddenHttpException($e->getMessage());
            }

            Email::send('order-customer', Yii::$app->params['adminEmail'], [
                'id' => $order->id,
                'information' => $order->htmlEmailFormattedInformation,
            ]);

            Email::send('order-customer', $order->email, [
                'id' => $order->id,
                'information' => $order->htmlEmailFormattedInformation,
            ]);

            return $this->redirect(['order']);
        } else {
            return $this->render('order-create', [
                'title' => 'Добавить заказ',
                'model' => $model,
            ]);
        }
    }

    public function actionAccount($id)
    {
        $member=Member::findOne($id);
        $user_id=$member->user_id;
        $user = User::findOne($user_id);
        
        if ($user->member->partner_id != $this->identity->entity->partner->id) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $model = new AccountForm(['user_id' => $user->id]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Account::swap(null, $user->getAccount($model->account_type), $model->amount, $model->message);

            return $this->redirect(['account', 'id' => $id, 'type' => $model->account_type]);
        }

        $accounts = [];
        $accountTypes = ArrayHelper::getColumn($user->accounts, 'type');
        foreach ($accountTypes as $accountType) {
            $account = $user->getAccount($accountType);
            if ($account) {
                $accounts[] = [
                    'type' => $account->type,
                    'name' => Html::makeTitle($account->typeName),
                    'account' => $account,
                    'dataProvider' => new ActiveDataProvider([
                        'id' => $account->type,
                        'query' => AccountLog::find()->where('account_id = :account_id', [':account_id' => $account->id]),
                        'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
                        'pagination' => [
                            'params' => array_merge($_GET, [
                                'type' => $account->type,
                            ]),
                        ],
                    ]),
                ];
            }
        }

        $accountType = Yii::$app->getRequest()->getQueryParam('type');
        if (!$user->getAccount($accountType)) {
            $accountType = Account::TYPE_DEPOSIT;
        }

        return $this->render('account', [
            'user' => $user,
            'model' => $model,
            'accounts' => $accounts,
            'accountType' => $accountType,
        ]);
    }

    public function actionUpdate($id)
    {
        $member = Member::findOne($id);

        if ($member->partner->id != $this->identity->entity->partner->id) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $model = new MemberForm([
            'isNewRecord' => false,
            'id' => $id,
            'user_id' => $member->user->id,
            'disabled' => $member->user->disabled,
            'phone' => $member->user->phone,
            'ext_phones' => $member->user->ext_phones,
            'firstname' => $member->user->firstname,
            'lastname' => $member->user->lastname,
            'patronymic' => $member->user->patronymic,
            'birthdate' => mb_substr($member->user->birthdate, 0, 10, Yii::$app->charset),
            'citizen' => $member->user->citizen,
            'registration' => $member->user->registration,
            'residence' => $member->user->residence,
            'passport' => $member->user->passport,
            'passport_date' => strtotime($member->user->passport_date) > 0 ? date('Y-m-d', strtotime($member->user->passport_date)) : '',
            'passport_department' => $member->user->passport_department,
            'itn' => $member->user->itn,
            'skills' => $member->user->skills,
            'recommender_info' => $member->user->recommender_info,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $member = Member::findOne($id);
            $member->user->disabled = $model->disabled;
            $member->user->phone = $model->phone;
            $member->user->ext_phones = $model->ext_phones;
            $member->user->firstname = $model->firstname;
            $member->user->lastname = $model->lastname;
            $member->user->patronymic = $model->patronymic;
            $member->user->birthdate = $model->birthdate;
            $member->user->citizen = $model->citizen;
            $member->user->registration = $model->registration;
            $member->user->residence = $model->residence && $model->residence != $model->registration ? $model->residence : null;
            $member->user->passport = preg_replace('/\D+/', '', $model->passport);
            $member->user->passport_date = $model->passport_date;
            $member->user->passport_department = $model->passport_department;
            $model->itn = preg_replace('/\D+/', '', $model->itn);
            $member->user->itn = $model->itn ? $model->itn : null;
            $member->user->skills = $model->skills ? $model->skills : null;
            $member->user->recommender_info = $model->recommender_info ? $model->recommender_info : null;

            $member->user->save();

            return $this->redirect(['/profile/partner/member']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}
