<?php

namespace app\modules\purchase\controllers\site;

use Yii;
use yii\web\Response;

use app\modules\purchase\models\PurchaseOrder;

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
    
    
}