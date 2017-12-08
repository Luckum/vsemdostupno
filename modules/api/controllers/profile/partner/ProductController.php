<?php

namespace app\modules\api\controllers\profile\partner;

use Yii;
use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\api\models\profile\admin\ProductAddition;
use app\models\User;
use app\models\Product;

class ProductController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search' => ['get'],
                    'add' => ['post'],
                ],
            ],
        ]);
    }

    public function actionSearch($q = null, $id = null)
    {
        $out = [
            'results' => [
                [
                    'id' => '',
                    'text' => '',
                ],
            ],
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!is_null($q)) {
            $productQuery = Product::find()
                ->orWhere('name like :q', [':q' => '%' . $q . '%'])
                ->andWhere('inventory != 0 OR inventory IS NULL')
                ->andWhere('visibility != 0')
                ->orderBy(['name' => SORT_ASC]);

            $data = [];
            foreach ($productQuery->each() as $product) {
                if ($product->inventory) {
                    $text = sprintf('%s (%s)', $product->name, $product->inventory);
                } else {
                    $text = $product->name;
                }
                $data[] = [
                    'id' => $product->id,
                    'text' => $text,
                ];
            }

            if ($data) {
                $out['results'] = $data;
            }
        } elseif ($id > 0) {
            $product = Product::find()
                ->andWhere('id = :id', [':id' => $id])
                ->andWhere('inventory != 0 OR inventory IS NULL')
                ->andWhere('visibility != 0')
                ->one();
            if ($product) {
                if ($product->inventory) {
                    $text = sprintf('%s (%s)', $product->name, $product->inventory);
                } else {
                    $text = $product->name;
                }
                $out['results'] = [
                    [
                        'id' => $product->id,
                        'text' => $text,
                    ],
                ];
            }
        }

        return $out;
    }

    public function actionAdd()
    {
        $productAddition = new ProductAddition();
        if (!$productAddition->load(Yii::$app->request->post()) || !$productAddition->validate()) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        $user = User::findOne($productAddition->user_id);
        $product = Product::findOne($productAddition->product_id);

        if (!$user || $user->disabled || !$product || !$product->visibility) {
            throw new ForbiddenHttpException('Действие не разрешено.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $quantity = $product->inventory && $product->inventory < $productAddition->quantity ?
            $product->inventory : $productAddition->quantity;
        $price = $product->getPriceByRole($user->role);
        $total = sprintf('%.2f', $quantity * $price);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
        ];
    }
}
