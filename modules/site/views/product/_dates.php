<?php

use yii\helpers\Url;
use kartik\helpers\Html;
use yii\bootstrap\Alert;

echo Alert::widget([
    'body' => sprintf(
        'Закупка состоится %s, заказы принимаются до %s включительно.',
        Html::a($purchase_date, Url::to([$url])),
        Html::a($stop_date, Url::to([$url]))
    ),
    'options' => [
        'class' => 'alert-info alert-def',
    ],
]);
?>