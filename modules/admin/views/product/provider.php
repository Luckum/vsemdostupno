<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\ProductFeature;
use app\models\ProductPrice;

$this->title = 'Товары поставщика "' . $provider->name . '"';
$this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => ['/admin/provider']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить товар', ['create?provider_id=' . $provider->id], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'label' => 'Название',
                'content' => function ($model) {
                    return $model->name . ProductFeature::getFeatureByProduct($model->id);
                }
            ],
            [
                'label' => 'Цена для участников',
                'content' => function ($model) {
                    return ProductPrice::getMemberPriceByProduct($model->id);
                }
            ],
            [
                'label' => 'Цена для всех',
                'content' => function ($model) {
                    return ProductPrice::getAllPriceByProduct($model->id);
                }
            ],
            [
                'label' => 'Количество',
                'content' => function ($model) {
                    return ProductFeature::getQuantityByProduct($model->id);
                }
            ],
            [
                'attribute' => 'visibility',
                'content' => function ($model) {
                    return '<input type="checkbox" ' . ($model->visibility ? 'checked' : '') . ' data-product-id="' . $model->id . '" class="update-visibility">';
                }
            ],
            [
                'attribute' => 'published',
                'content' => function ($model) {
                    return '<input type="checkbox" ' . ($model->published ? 'checked' : '') . ' data-product-id="' . $model->id . '" class="update-published">';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>