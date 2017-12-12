<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Product;
use app\models\ProductNewPrice;

class ApplyNewPriceController extends Controller
{
    public function actionIndex()
    {
        $products = ProductNewPrice::getProducts();
        if ($products) {
            foreach ($products as $product) {
                $model = Product::findOne($product->product_id);
                $model->inventory = $product->quantity;
                $model->purchase_price = $product->price;
                $model->partner_price = $model->price = $model->member_price = 0;
                $model->stock_date = $product->date;
                $model->scenario = 'apply_product';
                if ($model->save()) {
                    $product->delete();
                }
            }
        }
    }
}