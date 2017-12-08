<?php

use yii\web\View;
use yii\bootstrap\Alert;
use yii\web\JsExpression;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\icons\Icon;
use app\models\Category;
use dosamigos\gallery\Gallery;

/* @var $this yii\web\View */
$this->title = $model->name;

$category = null;
$url = Yii::$app->request->referrer;
if (preg_match('/\/category\/\d+$/', $url)) {
    $categoryId = preg_replace('/^\D+/', '', $url);
    $category = Category::find()
        ->where('visibility != 0 AND id = :id', [':id' => $categoryId])
        ->one();
    if ($category) {
        $this->params['breadcrumbs'] = $category->getBreadcrumbs($model->name);
    }
}
if (!$category && $model->categories) {
    $category = $model->categories[0];
    $this->params['breadcrumbs'] = $category->getBreadcrumbs($model->name);
}

$serviceImages = [];
foreach ($model->serviceHasPhoto as $serviceHasPhoto) {
    $serviceImages[] = [
        'url' => $serviceHasPhoto->imageUrl,
        'src' => $model->thumbUrl,
        'options' => ['class' => 'hidden'],
    ];
}

?>

<?= Html::pageHeader(Html::encode($model->name)) ?>

<div class="row">
    <div class="col-md-6">
        <?= Gallery::widget(['id' => 'service-images', 'items' => $serviceImages]) ?>
        <?= Html::a(
                Html::img($model->thumbUrl),
                '#',
                [
                    'class' => 'thumbnail',
                    'onclick' => new JsExpression('
                        $("#service-images a").first().trigger("click");
                        return false;
                    '),
                ]
        ) ?>
    </div>
    <?php if ($model->calculatedPrice > 0): ?>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $prices = [
                            [
                                'content' => 'Цена для всех желающих',
                                'badge' => $model->formattedPrice,
                                'options' => ['class' => $model->price != $model->calculatedPrice ? 'disabled' : ''],
                            ],
                            [
                                'content' => 'Цена для участников ПО',
                                'badge' => $model->formattedMemberPrice,
                                'options' => ['class' => $model->member_price != $model->calculatedPrice ? 'disabled' : ''],
                            ],
                        ];

                        echo Html::panel([
                                'heading' => Icon::show('tags') . ' Цены',
                                'postBody' => Html::listGroup($prices),
                                'headingTitle' => true,
                            ],
                            Html::TYPE_PRIMARY) ?>
                </div>
            </div>
        </div>
    <?php endif ?>
    <?php if ($model->contacts): ?>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 robots-nocontent">
                    <!--googleoff: all-->
                    <!--noindex-->
                        <?php
                            echo Html::panel([
                                    'heading' => Icon::show('pencil-square-o') . ' Контакты',
                                    'postBody' => Html::tag('div', nl2br(Html::encode($model->contacts)), ['class' => 'contacts']),
                                    'headingTitle' => true,
                                ],
                                Html::TYPE_PRIMARY) ?>
                    <!--/noindex-->
                    <!--googleon: all-->
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<div class="service-description">
    <div class="row">
        <div class="col-md-12">
            <?= Html::tag('h2', 'Описание') ?>
            <?= $model->description ?>
        </div>
    </div>
</div>
