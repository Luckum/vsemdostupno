<?php

namespace app\modules\purchase\controllers\admin;

use Yii;
use app\models\CandidateGroup;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\modules\purchase\models\PurchaseProduct;
use app\models\Product;
use app\models\ProductFeature;
use app\models\ProductPrice;

/**
 * Default controller for the `purchase` module
 */
class DefaultController extends BaseController
{
    public function actionCreate()
    {
        $dataProvider = new ActiveDataProvider([
           'query' => PurchaseProduct::find()->where('NOW() < purchase_date')->orderBy('purchase_date'),
           'sort' => false,
        ]);
        
        return $this->render('create', [
            'model' => new PurchaseProduct,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionGetProducts()
    {
        $provider_id = $_POST['provider_id'];
        $products = Product::getProductsByProvider($provider_id)->getModels();
        $data = [];
        if ($products) {
            foreach ($products as $k => $val) {
                if ($val->categoryHasProduct[0]->category->isPurchase()) {
                    $data[$val->categoryHasProduct[0]->category->name][$val->id] = $val->name;
                }
            }
        }
        
        return $this->renderPartial('_products', [
            'data' => $data,
        ]);
    }
    
    public function actionGetProduct()
    {
        $product_id = $_POST['product_id'];

        $product = Product::find()->joinWith('productFeatures')->joinWith('productFeatures.productPrices')->where(['product.id' => $product_id])->one();
        return $this->renderPartial('_form', [
            'product' => $product,
        ]);
    }
    
    public function actionGetFeature()
    {
        $id = $_POST['id'];
        $feature = ProductFeature::find()->joinWith('productPrices')->where(['product_feature.id' => $id])->one();
        $res = [
            'tare' => $feature->tare,
            'volume' => $feature->volume,
            'measurement' => $feature->measurement,
            'price' => $feature->productPrices[0]->purchase_price,
            'is_weights' => $feature->is_weights,
        ];
        return json_encode($res);
    }
    
    public function actionAddProduct() 
    {
        $volume = ($_POST['product_exists'] == '0') ? $_POST['volume'] : $_POST['volume_ex'];
        $tare = ($_POST['product_exists']) == '0' ? $_POST['tare'] : $_POST['tare_ex'];
        $measurement = ($_POST['product_exists'] == '0') ? $_POST['measurement'] : $_POST['measurement_ex'];
        $price = ($_POST['product_exists'] == '0') ? $_POST['summ'] : $_POST['summ_ex'];
        $comment = ($_POST['product_exists'] == '0') ? $_POST['comment'] : $_POST['comment_ex'];
        $deposit = ($_POST['product_exists'] == '0') ? (isset($_POST['send_notification']) ? 1 : 0) : (isset($_POST['send_notification_ex']) ? 1 : 0);
        $is_weights = ($_POST['product_exists'] == '0') ? (isset($_POST['is_weights']) ? 1 : 0) : (isset($_POST['is_weights_ex']) ? 1 : 0);
        
        $product = Product::find()->where(['id' => $_POST['product-id']])->one();
        $product_feature = ProductFeature::find()
            ->where([
                'product_id' => $product->id,
                'volume' => $volume,
                'measurement' => $measurement,
                'tare' => $tare,
                'is_weights' => $is_weights])
            ->one();
        if (!$product_feature) {
            $product_feature = new ProductFeature();
            $product_feature->product_id = $product->id;
            $product_feature->volume = $volume;
            $product_feature->measurement = $measurement;
            $product_feature->tare = $tare;
            $product_feature->quantity = 0;
            $product_feature->is_weights = $is_weights;
            $product_feature->save();
            
            $product_price = new ProductPrice();
            $product_price->product_id = $product->id;
            $product_price->product_feature_id = $product_feature->id;
            $product_price->purchase_price = $price;
            $product_price->save();
        }
        
        $purchase = new PurchaseProduct;
        $purchase->created_date = $_POST['PurchaseProduct']['created_date'];
        $purchase->purchase_date = $_POST['PurchaseProduct']['purchase_date'];
        $purchase->stop_date = $_POST['PurchaseProduct']['stop_date'];
        $purchase->renewal = isset($_POST['PurchaseProduct']['renewal']) ? 1 : 0;
        $purchase->purchase_total = $_POST['PurchaseProduct']['purchase_total'];
        $purchase->is_weights = $is_weights;
        $purchase->tare = $tare;
        $purchase->weight = $volume;
        $purchase->measurement = $measurement;
        $purchase->summ = $price;
        $purchase->product_feature_id = $product_feature->id;
        $purchase->provider_id = $_POST['PurchaseProduct']['provider_id'];
        $purchase->comment = $comment;
        $purchase->send_notification = isset($_POST['send_notification']) ? 1 : 0;
        $purchase->status = 'advance';
        $purchase->save();
        //print_r($purchase);
        
        return true;
    }
    
    public function actionCnangeRenewal()
    {
        $purchase = PurchaseProduct::findOne($_POST['id']);
        $purchase->renewal = $_POST['checked'];
        $purchase->save();
        return true;
    } 
}