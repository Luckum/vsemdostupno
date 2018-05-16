<?php

namespace app\modules\purchase\controllers\site;

use Yii;
use yii\web\Response;

use app\modules\purchase\models\PurchaseOrder;
use app\modules\purchase\models\PurchaseOrderProduct;
use app\modules\purchase\models\PurchaseProduct;

class HistoryController extends BaseController
{
    public function actionIndex()
    {
        $purchases_date = PurchaseOrder::getPurchaseDatesByUser(Yii::$app->user->identity->id);
        
        return $this->render('index', [
            'purchases_date' => $purchases_date
        ]);
    }
    
    public function actionDetails($date)
    {
        $dataProvider = PurchaseOrder::getDetalizationByUser(Yii::$app->user->identity->id, $date);
        return $this->render('details', [
            'dataProvider' => $dataProvider,
            'date' => $date
        ]);
    }
    
    public function actionReorder($order_id, $date)
    {
        $order = PurchaseOrder::findOne($order_id);
        $new_order_total = 0;
        
        
        $new_order = new PurchaseOrder;
        $new_order->city_id = $order->city_id;
        $new_order->partner_id = $order->partner_id;
        $new_order->user_id = $order->user_id;
        $new_order->role = $order->role;
        $new_order->city_name = $order->city_name;
        $new_order->partner_name = $order->partner_name;
        $new_order->email = $order->email;
        $new_order->phone = $order->phone;
        $new_order->firstname = $order->firstname;
        $new_order->lastname = $order->lastname;
        $new_order->patronymic = $order->patronymic;
        $new_order->address = $order->address;
        $new_order->comment = $order->comment;
        $new_order->save();
        
        foreach ($order->purchaseOrderProducts as $product) {
            if ($product->purchaseProduct->purchase_date == $date) {
                $copy = PurchaseProduct::find()->where(['copy' => $product->purchase_product_id])->one();
                if ($copy) {
                    $new_product = new PurchaseOrderProduct;
                    $new_product->purchase_order_id = $new_order->id;
                    $new_product->product_id = $product->product_id;
                    $new_product->purchase_product_id = $copy->id;
                    $new_product->name = $product->name;
                    $new_product->price = $product->price;
                    $new_product->quantity = $product->quantity;
                    $new_product->total = $product->total;
                    $new_product->purchase_price = $product->purchase_price;
                    $new_product->provider_id = $product->provider_id;
                    $new_product->product_feature_id = $product->product_feature_id;
                    $new_product->status = 'advance';
                    $new_product->save();
                    
                    $new_order_total += $new_product->total;
                } else {
                    
                }
            }
        }
    }
}