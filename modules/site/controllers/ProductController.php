<?php

namespace app\modules\site\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Product;
use app\models\ProductFeature;
use app\models\ProductPrice;
use app\models\Cart;
use app\models\Category;

class ProductController extends BaseController
{
    public function actionIndex($id)
    {
        $model = Product::find()
            ->joinWith('productFeatures')
            ->joinWith('productFeatures.productPrices')
            ->joinWith('categoryHasProduct')
            ->joinWith('categoryHasProduct.category')
            ->andWhere('product.id = :id', [':id' => $id])
            ->andWhere('product.visibility != 0')
            ->andWhere('published != 0')
            ->one();

        if (!$model->isPurchase()) {
            $model = Product::find()
            ->joinWith('productFeatures')
            ->joinWith('productFeatures.productPrices')
            ->andWhere('product.id = :id', [':id' => $id])
            ->andWhere('product.visibility != 0')
            ->andWhere('published != 0')
            ->andWhere('product_feature.quantity > 0')
            ->one();
        }
        
        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
    
    public function actionGetPrices()
    {
        $feature_id = $_POST['f_id'];
        return $this->renderPartial('_prices', [
            'all_price' => Product::getFormattedPriceFeature($feature_id),
            'member_price' => Product::getFormattedMemberPriceFeature($feature_id),
        ]);
    }
    
    public function actionInCart()
    {
        $feature_id = $_POST['f_id'];
        $feature = ProductFeature::findOne($feature_id);
        return Cart::hasProductId($feature);
    }
}
