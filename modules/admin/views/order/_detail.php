<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\web\JsExpression;
use kartik\dropdown\DropdownX;
use app\models\OrderStatus;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$updateOrderStatusUrl = Url::to(['/api/profile/admin/order/update-status']);
$script = <<<JS
    function updateOrderStatus(orderId, orderStatusId) {
        $.ajax({
            url: '$updateOrderStatusUrl',
            type: 'POST',
            data: {
                orderId: orderId,
                orderStatusId: orderStatusId
            },
            success: function (data) {
                if (!(data && data.success)) {
                    alert('Ошибка обновления статуса заказа');
                }
            },
            error: function () {
                alert('Ошибка обновления статуса заказа');
            },
        });

        return false;
    }
JS;
$this->registerJs($script, $this::POS_END);
?>
<div class="order-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'content' => function($model) {
                    return empty($model->role) ? sprintf("%'.05d\n", $model->order_id) . " От Гостя" : sprintf("%'.05d\n", $model->order_id) . " От Участника";
                },
            ],
            'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Статус',
                'template' => '{orderStatus}',
                'buttons' => [
                    'orderStatus' => function ($url, $model) {
                        return Html::dropDownList(
                            'order-status-select-' . $model->id,
                            $model->order_status_id,
                            ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'), [
                                'onchange' => new JsExpression("
                                    return updateOrderStatus(". $model->id .", $(this).val());
                                "),
                            ]
                        );
                    }
                ],
            ],

            'htmlFormattedInformation:raw',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{actions}{hide}',
                'buttons' => [
                    'actions' => function ($url, $model) {
                        return Html::beginTag('div', ['class'=>'dropdown']) .
                            Html::button('Действия <span class="caret"></span>', [
                                'type'=>'button',
                                'class'=>'btn btn-default',
                                'data-toggle'=>'dropdown'
                            ]) .
                            DropdownX::widget([
                            'items' => [
                                [
                                    'label' => 'Прих. ордер',
                                    'url' => Url::to(['/admin/order/download-order', 'id' => $model->id]),
                                ],
                                [
                                    'label' => 'Акт возврата',
                                    'url' => Url::to(['/admin/order/download-act', 'id' => $model->id]),
                                ],
                                [
                                    'label' => 'Заявка',
                                    'url' => Url::to(['/admin/order/download-request', 'id' => $model->id]),
                                ],
                                [
                                    'label' => 'Акт возврата паевого взноса',
                                    'url' => Url::to(['/admin/order/download-return-fee-act', 'id' => $model->id]),
                                ],
                                '<li class="divider"></li>',
                                [
                                    'label' => 'Удалить',
                                    'url' => 'javascript:void(0)',
                                    'linkOptions' => [
                                        'data' => [
                                            'order-id' => $model->id
                                        ],
                                        'onclick' => 'deleteOrderStock(this);',
                                    ],
                                    'visible' => Yii::$app->user->identity->entity->role == User::ROLE_SUPERADMIN
                                ],
                                [
                                    'label' => 'Сделать возврат и удалить',
                                    'url' => 'javascript:void(0)',
                                    'linkOptions' => [
                                        'data' => [
                                            'order-id' => $model->id
                                        ],
                                        'onclick' => 'deleteReturnOrderStock(this);',
                                    ],
                                    'visible' => Yii::$app->user->identity->entity->role == User::ROLE_SUPERADMIN
                                ],
                            ],
                        ]) .
                        Html::endTag('div');
                    },
                    'hide' => function($url, $model) use ($date_e, $date_s) {
                        return Html::button('Скрыть', [
                                'type'=>'button',
                                'class'=>'btn btn-primary',
                                'style' => 'margin-top: 10px',
                                'onclick' => 'hideOrderStock(this);',
                                'data-order-id' => $model->id,
                                'data-date-e' => $date_e,
                                'data-date-s' => $date_s,
                            ]);
                    }
                ],
            ],
        ],
    ]); ?>

</div>
