<?php

use yii\helpers\Url;
use yii\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
$this->title = "Поиск";
$this->params['breadcrumbs'] = [$this->title];

?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<div class="order-index">
    <p>
    
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'created_at',
            'htmlFormattedInformation:raw',
        ],
    ]); ?>
</div>
