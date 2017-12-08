<?php

use yii\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
$this->title = $title;
$this->params['breadcrumbs'] = [$this->title];

?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'created_at',
        'htmlFormattedInformation:raw',
    ],
]); ?>
