<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\web\JsExpression;
use kartik\dropdown\DropdownX;
use app\models\OrderStatus;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\StockHead*/


$this->title = 'Учёт товаров/остатки';
$this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => '/admin/provider'];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .grid-view th {
        white-space: normal;
    }

    form>div {
        padding-bottom: 20px;
    }
    form {
        display: inline-block;
    }
    .btn-success{
        margin-bottom: 10px;
    }

</style>
<div class="stock-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form=ActiveForm::begin(['action'=>'/admin/stock/filter', 'method'=>'GET']);
    ?>
    <label for="date_from">Дата начала фильтрации</label>
    <?php echo DatePicker::widget([
        'name'=>'from_date',
        'id'=>'date_from',
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'readonly' => true,
        'layout' => '{input}{picker}',
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
    ]);?>
    <label for="date_to">Конечная дата фильтрации</label>
    <?php echo DatePicker::widget([
        'name'=>'to_date',
        'id'=>'date_to',
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'readonly' => true,

        'layout' => '{input}{picker}',
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
    ]);
    ?>
    <?= Html::submitButton('Фильтровать', ['class'=>'btn btn-success']);?>

    <?php ActiveForm::end();?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'stock_body.stockHead.date',
                'headerOptions' => ['style' => 'min-width: 120px;']
            ],
            [
                'attribute' => 'stock_body.stockHead.provider_id',
                'label' => 'Поставщик',
                'content'=>function($data){
                    return $data->stock_body->stockHead->providerName;
                }
            ],
            [
                'attribute' => 'stock_body.product_id',
                'content' => function($data) {
                    return $data->stock_body->productName;
                }
            ],
            'stock_body.tare',
            'stock_body.weight',
            'stock_body.measurement',
            'total_rent',
            'stock_body.summ',
            'total_sum',
            'reaminder_rent',
            [
                'attribute' => 'summ_reminder',
                'headerOptions' => ['style' => 'min-width: 80px;']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{actions}',
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
                                    'label' => 'История поставок',
                                    'url' => Url::to(['view', 'id' => $model->stock_body->stockHead->provider_id]),
                                ],
                            ],
                        ]) .
                        Html::endTag('div');
                    }
                ],
            ],
        ],
    ]); ?>

</div>