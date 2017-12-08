<?php
use kartik\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\helpers\NumberColumn;

$this->title = 'Заказы поставщикам';
$this->params['breadcrumbs'] = [$this->title];
?>

<?= Html::pageHeader(Html::encode($this->title)) ?>
<h4>Заявка на поставку товаров на <?= date('d.m.Y', strtotime($date['end'])); ?></h4>
<div class="order-index">
    <a href="<?= Url::to(['/profile/provider/order/hide', 'date_e' => date('Y-m-d', strtotime($date['end'])), 'date_s' => date('Y-m-d', strtotime($date['start']))]); ?>">Удалить заявку</a>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn', 
                'header' => '№ п/п', 
                'footer' => 'ИТОГО:',
                'footerOptions' => ['colspan' => 4]
            ],
            [
                'label' => 'Наименование товаров', 
                'value' => function ($data) {
                    return $data['product_name'];
                },
                'footerOptions' => ['style' => 'display: none;']
            ],
            [
                'label' => 'Поставщик',
                'value' => function ($data) {
                    return $data['provider_name'];
                },
                'footerOptions' => ['style' => 'display: none;']
            ],
            [
                'label' => 'Количество',
                'format' => 'raw',
                'contentOptions' => ['style' => 'font-weight: 600;'],
                'value' => function ($data) use ($date) {
                    return Html::a(number_format($data['quantity']), Url::to(['/profile/provider/order/detail', 'id' => $data['product_id'], 'prid' => $data['provider_id'], 'date' => date('Y-m-d', strtotime($date['end']))]), ['style' => 'text-decoration: underline;']);
                },
                'footerOptions' => ['style' => 'display: none;']
            ],
            [
                'label' => 'На сумму',
                'contentOptions' => ['style' => 'font-weight: 600;'],
                'value' => function ($data) {
                    return $data['total'];
                },
                'class' => NumberColumn::className(),
                'footerOptions' => ['style' => 'font-weight: 600;'],
            ]
        ],
        'showFooter' => true,
    ]);?>
</div>