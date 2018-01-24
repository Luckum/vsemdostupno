<?php

namespace app\modules\site\controllers\profile\provider;

use Yii;
use app\modules\site\controllers\BaseController;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Order;
use app\models\Partner;
use app\models\Product;
use app\models\Provider;
use yii\web\ForbiddenHttpException;

class OrderController extends BaseController
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
                            'detail',
                            'hide'
                        ],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (in_array(Yii::$app->user->identity->role, [User::ROLE_ADMIN])) {
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
        $dateEnd = date('Y-m-d 21:00:00');
        $dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m'), date('d') - 1, date('Y')));
        
        $partner = Partner::getByUserId(Yii::$app->user->identity->id);
        $dataProvider = Order::getProviderOrderByPartner($partner->id, ['start' => $dateStart, 'end' => $dateEnd]);
        
        return $this->render('index', [
            'date' => ['start' => $dateStart, 'end' => $dateEnd],
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionDetail($id, $prid, $date)
    {
        $dateEnd = date('Y-m-d 21:00:00');
        $dateStart = date('Y-m-d H:i:s', mktime(21, 0, 0, date('m'), date('d') - 1, date('Y')));
        $partner = Partner::getByUserId(Yii::$app->user->identity->id);
        //$product = Product::findOne($id);
        $provider = Provider::findOne($prid);
        $details = Order::getProviderOrderDetails($id, ['start' => $dateStart, 'end' => $dateEnd], $partner->id);
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