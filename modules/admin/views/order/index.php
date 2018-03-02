<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Order;
use app\models\ProviderNotification;
use app\models\Provider;
use app\models\ProductFeature;
use app\models\User;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\SqlDataProvider */
/* @var $dataProvider1 yii\data\ActiveDataProvider */
$this->title = 'Заказы на склад';
$this->params['breadcrumbs'][] = $this->title;
$delete_action = Yii::$app->user->identity->entity->role == User::ROLE_SUPERADMIN ? 'delete-stock' : 'admin-delete';
//$models = $dataProvider->getModels();
//$total_price = 0;

/*echo '<pre>';
var_dump($test);
die();*/

?>
<div class="member-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <table class="table table-bordered">
        <thead>
            <th style="vertical-align: top;">Дата</th>
            <th></th>
        </thead>
        <tbody>
            <?php foreach ($dates as $date): ?>
                <tr>
                    <td>
                        <a href="<?= Url::to(['/admin/order/date', 'date' => date('Y-m-d', strtotime($date['end']))]); ?>"><?= date('d.m.Y', strtotime($date['end'])); ?></a>
                    </td>
                    <td>
                        <a href="<?= Url::to([$delete_action, 'date' => date('Y-m-d', strtotime($date['end']))]) ?>" title="Удалить" data-pjax="0" data-method="post" data-confirm="Вы уверены что хотите удалить заказ?">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>