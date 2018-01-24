<?php
namespace app\modules\admin\controllers;
use Yii;
use app\models\Order;
use app\models\Partner;
use app\models\Product;
use app\models\Provider;

class ProviderOrderController extends BaseController
{
    public function actionIndex()
    {   
        $dataProviderAll = $dates = [];
        $orders_date = Order::getOrdersDate();
        if ($orders_date) {
            foreach ($orders_date as $k => $date) {
                $dateInit = strtotime($date['order_date']);
                $dateEnd = date('Y-m-d 21:00:00', $dateInit);
                $dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m', $dateInit), date('d', $dateInit) - 1, date('Y', $dateInit)));
                $dataProvider = Order::getProvidersOrder($dateStart, $dateEnd);
                $dataProviderAll[] = $dataProvider;
                $dates[] = ['start' => $dateStart, 'end' => $dateEnd];
                
                if ($k != 0) {
                    $nextDate = $orders_date[$k - 1]['order_date'];
                    $datesDiff = (strtotime($nextDate) - strtotime($date['order_date']))/3600/24;
                    if ($datesDiff > 1) {
                        $dateStart = date('Y-m-d 21:00:00', $dateInit);
                        $dateEnd = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m', $dateInit), date('d', $dateInit) + 1, date('Y', $dateInit)));
                        $dataProvider = Order::getProvidersOrder($dateStart, $dateEnd);
                        $dataProviderAll[] = $dataProvider;
                        $dates[] = ['start' => $dateStart, 'end' => $dateEnd];
                    }
                }
            }
        }
        //$dateEnd = date('Y-m-d 21:00:00');
        //$dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m'), date('d') - 1, date('Y')));
        
        return $this->render('index',[
            //'dataProvider' => Order::getProvidersOrder($dateStart, $dateEnd),
            //'date' => ['start' => $dateStart, 'end' => $dateEnd],
            'dataProviderAll' => $dataProviderAll,
            'dates' => $dates,
        ]);
    }
    
    public function actionDetail($id, $pid, $prid, $date)
    {
        //$dateEnd = date('Y-m-d 21:00:00');
        //$dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m'), date('d') - 1, date('Y')));
        //$dateEnd = "2017-10-08 21:00:00";
        //$dateStart = "2017-10-07 21:00:00";
        
        $dateInit = strtotime($date);
        $dateEnd = date('Y-m-d 21:00:00', $dateInit);
        $dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m', $dateInit), date('d', $dateInit) - 1, date('Y', $dateInit)));
        $partner = Partner::findOne($pid);
        //$product = Product::findOne($id);
        $provider = Provider::findOne($prid);
        $details = Order::getProviderOrderDetails($id, ['start' => $dateStart, 'end' => $dateEnd], $pid);
        return $this->render('detail', [
            'partner' => $partner,
            //'product' => $product,
            'provider' => $provider,
            'date' => $date,
            'details' => $details,
        ]);
    }
    
    public function actionHide($date_e, $date_s)
    {
        $dateEnd = date('Y-m-d 21:00:00', strtotime($date_e));
        $dateStart = date('Y-m-d 21:00:00', strtotime($date_s));
        
        $orders = Order::hideOrdersByDate(['start' => $dateStart, 'end' => $dateEnd]);
        return $this->redirect(['index']);
    }
}
?>