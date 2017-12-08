<?php

namespace app\modules\api\controllers\cart;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Product;
use app\models\Cart;
use app\modules\api\models\cart\ProductAddition;
use app\modules\api\models\cart\ProductUpdating;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest &&
                                in_array(Yii::$app->user->identity->role, [User::ROLE_ADMIN])) {
                                throw new ForbiddenHttpException('Действие не разрешено.');
                            }

                            return true;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add' => ['post'],
                    'update' => ['post'],
                    'clear' => ['post'],
                ],
            ],
        ];
    }

    public function actionAdd()
    {
        $productAddition = new ProductAddition();
        if (!$productAddition->load(Yii::$app->request->post()) || !$productAddition->validate()) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $product = Product::findOne($productAddition->id);
        $cart = new Cart();

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Товар не найден.',
            ];
        }

        if (Yii::$app->user->isGuest && $product->only_member_purchase) {
            return [
                'success' => false,
                'message' => 'Данное предложение доступно только для участников.',
            ];
        }

        if ($product->orderDate && (strtotime($product->orderDate) + strtotime('1 day', 0)) < time()) {
            $product->quantity = 0;
            $cart->update($product, $product->quantity);

            return [
                'success' => false,
                'message' => sprintf(
                    'Товар недоступен для заказа. Прием заказов закончился "%s"',
                    Yii::$app->formatter->asDate(strtotime($product->orderDate) + strtotime('1 day', 0), 'long')
                ),
                'cartInformation' => $cart->information,
                'productQuantity' => $product->quantity,
                'productInformation' => $product->formattedCalculatedTotalPrice,
                'order' => $cart->total != 0,
            ];
        }

        $product->quantity = $productAddition->quantity;
        $product->quantity = $cart->add($product, $product->quantity);

        return [
            'success' => true,
            'message' => 'Товар добавлен в корзину!',
            'cartInformation' => $cart->information,
            'productQuantity' => $product->quantity,
            'productInformation' => $product->formattedCalculatedTotalPrice,
            'order' => $cart->total != 0,
        ];
    }

    public function actionUpdate()
    {
        $productUpdating = new ProductUpdating();
        if (!$productUpdating->load(Yii::$app->request->post()) || !$productUpdating->validate()) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $product = Product::findOne($productUpdating->id);
        $cart = new Cart();

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Товар не найден.',
            ];
        }

        if ($product->orderDate && (strtotime($product->orderDate) + strtotime('1 day', 0)) < time()) {
            $product->quantity = 0;
            $cart->update($product, $product->quantity);

            return [
                'success' => false,
                'message' => sprintf(
                    'Товар недоступен для заказа. Прием заказов закончился "%s"',
                    Yii::$app->formatter->asDate(strtotime($product->orderDate) + strtotime('1 day', 0), 'long')
                ),
                'cartInformation' => $cart->information,
                'productQuantity' => $product->quantity,
                'productInformation' => $product->formattedCalculatedTotalPrice,
                'order' => $cart->total != 0,
            ];
        }

        $product->quantity = $productUpdating->quantity;
        $product->quantity = $cart->update($product, $product->quantity);

        return [
            'success' => true,
            'message' => 'Количество товара в корзине обновлено!',
            'cartInformation' => $cart->information,
            'productQuantity' => $product->quantity,
            'productInformation' => $product->formattedCalculatedTotalPrice,
            'order' => $cart->total != 0,
        ];
    }

    public function actionClear()
    {
        $cart = new Cart();
        $cart->clear();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => true,
            'message' => 'Корзина очищена!',
        ];
    }
}
