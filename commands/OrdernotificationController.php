<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Order;
use app\models\Partner;
use app\models\Provider;
use app\models\ProviderNotification;
use app\models\User;

class OrdernotificationController extends Controller
{
    public function actionIndex()
    {
        $dateEnd = date('Y-m-d 21:00:00');
        $dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m'), date('d') - 1, date('Y')));
        
        $providers = Order::getProviderIdByDate(['start' => $dateStart, 'end' => $dateEnd]);
        if ($providers) {
            foreach ($providers as $provider) {
                if ($provider['provider_id'] != 0) {
                    $partners = Order::getPartnerIdByProvider(['start' => $dateStart, 'end' => $dateEnd], $provider['provider_id']);
                    if ($partners) {
                        foreach ($partners as $partner) {
                            $details = Order::getOrderDetailsByProviderPartner(['start' => $dateStart, 'end' => $dateEnd], $provider['provider_id'], $partner['partner_id']);
                            if ($details) {
                                $this->sendEmailToProvider($details, $provider['provider_id'], $partner['partner_id'], $dateEnd);
                                foreach ($details as $detail) {
                                    if (!ProviderNotification::find()->where(['order_date' => $dateEnd, 'provider_id' => $provider['provider_id'], 'product_id' => $detail['product_id']])->exists()) {
                                        $notif = new ProviderNotification;
                                        $notif->order_date = $dateEnd;
                                        $notif->provider_id = $provider['provider_id'];
                                        $notif->product_id = $detail['product_id'];
                                        $notif->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $dataProvider = Order::getProvidersOrder($dateStart, $dateEnd);
        $this->sendEmailToAdmin($dataProvider, ['start' => $dateStart, 'end' => $dateEnd]);
        
        $partners = Order::getPartnerIdByDate(['start' => $dateStart, 'end' => $dateEnd]);
        if ($partners) {
            foreach ($partners as $partner) {
                $dataProvider = Order::getProviderOrderByPartner($partner['partner_id'], ['start' => $dateStart, 'end' => $dateEnd]);
                $this->sendEmailToPartner($dataProvider, ['start' => $dateStart, 'end' => $dateEnd], $partner['partner_id']);
            }
        }
    }
    
    protected function sendEmailToProvider($details, $provider_id, $partner_id, $date)
    {
        $provider = Provider::find()->where(['id' => $provider_id])->with('user')->one();
        $partner = Partner::findOne($partner_id);
        Yii::$app->mailer->compose('provider/order', [
                'details' => $details,
                'partner' => $partner,
                'date' => $date
            ])
            ->setFrom(Yii::$app->params['fromEmail'])
            ->setTo($provider->user->email)
            ->setSubject('Поступил заказ с сайта "' . Yii::$app->params['name'] . '"')
            ->send();
    }
    
    protected function sendEmailToAdmin($dataProvider, $date)
    {
        $admin = User::find()->where(['role' => 'admin'])->one();
        Yii::$app->mailer->compose('admin/order', [
                'dataProvider' => $dataProvider,
                'date' => $date
            ])
            ->setFrom(Yii::$app->params['fromEmail'])
            ->setTo($admin->email)
            ->setSubject('Заявка на поставку товаров с сайта "' . Yii::$app->params['name'] . '"')
            ->send();
    }
    
    protected function sendEmailToPartner($dataProvider, $date, $partner_id)
    {
        $partner = Partner::find()->where(['id' => $partner_id])->with('user')->one();
        Yii::$app->mailer->compose('partner/order', [
                'dataProvider' => $dataProvider,
                'date' => $date
            ])
            ->setFrom(Yii::$app->params['fromEmail'])
            ->setTo($partner->user->email)
            ->setSubject('Заявка на поставку товаров с сайта "' . Yii::$app->params['name'] . '"')
            ->send();
    }
}