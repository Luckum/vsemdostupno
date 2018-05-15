<?php

use kartik\helpers\Html;
use yii\web\View;
use app\models\Category;

/* @var $this yii\web\View */
$this->title = Yii::$app->params['name'];

/*$panels = [
    [
        'view' => '@app/modules/site/views/category/snippets/product-panel.php',
        'params' => [
            'name' => 'Новинки',
            'products' => $newProducts,
        ],
    ],
    [
        'view' => '@app/modules/site/views/category/snippets/product-panel.php',
        'params' => [
            'name' => 'Закупки',
            'products' => $purchaseProducts,
        ],
    ],
    [
        'view' => '@app/modules/site/views/category/snippets/product-panel.php',
        'params' => [
            'name' => 'Спецпредложения',
            'products' => $featuredProducts,
        ],
    ],
    [
        'view' => '@app/modules/site/views/category/snippets/service-panel.php',
        'params' => [
            'name' => 'Услуги',
            'services' => $services,
        ],
    ],
];

foreach ($panels as $panel) {
    echo $this->renderFile($panel['view'], $panel['params']);
}*/


?>

<div class="row product-panel">
    <div id="main-cat-level-1">
        <?php foreach ($menu_first_level as $item): ?>
            <div class="col-md-4">
                <?= Html::a(
                        Html::img($item->thumbUrl),
                        $item->url,
                        ['class' => 'thumbnail']
                ) ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php foreach ($menu_first_level as $f_level): ?>
        <div id="main-cat-level-2-<?= $f_level->id ?>" class="main-cat-level-2" style="display: none;">
            <?php $categories = Category::getMenuItems($f_level); ?>
            <?php if ($categories): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-4">
                        <?= Html::a(
                                Html::img($cat['thumbUrl']),
                                $cat['url'],
                                ['class' => 'thumbnail']
                        ) ?>
                        <h5 class="text-center" style="font-size: 20px;"><strong><?= $cat['content'] ?></strong></h5>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php $productsQuery = $f_level->getAllProductsQuery()
                        ->andWhere('visibility != 0')
                        ->andWhere('published != 0'); 
                    $products = $productsQuery->all();
                ?>
                <?php if ($products): ?>
                    <div class="row text-center">
                        <?php foreach ($products as $val): ?>
                            <div class="col-md-3 product-item">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::a(
                                            Html::img($val->thumbUrl),
                                            $val->url,
                                            ['class' => 'thumbnail']
                                        ) ?>
                                    </div>
                                </div>
                                <div class="row product-name">
                                    <div class="col-md-12">
                                        <?= Html::tag('h5', Html::encode($val->name)) ?>
                                    </div>
                                </div>
                                <div class="row product-price">
                                    <div class="col-md-12">
                                        <?php if (Yii::$app->user->isGuest): ?>
                                            <?= $val->productFeatures[0]->is_weights == 1 ? Html::badge(Yii::$app->formatter->asCurrency($val->formattedPrice * $val->productFeatures[0]->volume, 'RUB') , ['class' => '']) : Html::badge($val->formattedPrice, ['class' => '']) ?>
                                        <?php else: ?>
                                            <?= $val->productFeatures[0]->is_weights == 1 ? Html::badge(Yii::$app->formatter->asCurrency($val->formattedMemberPrice * $val->productFeatures[0]->volume, 'RUB') , ['class' => '']) : Html::badge($val->formattedMemberPrice, ['class' => '']) ?>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>