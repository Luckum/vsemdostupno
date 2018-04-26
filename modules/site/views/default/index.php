<?php

use yii\helpers\Html;
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
        <?php if (Yii::$app->hasModule('purchase') && $purchase && $purchase->visibility): ?>
            <div class="col-md-4">
                <?= Html::a(
                        Html::img($purchase->thumbUrl),
                        $purchase->url,
                        ['class' => 'thumbnail']
                ) ?>
            </div>
        <?php endif; ?>
        <?php if ($catalogue && $catalogue->visibility): ?>
            <div class="col-md-4">
                <?= Html::a(
                        Html::img($catalogue->thumbUrl),
                        $catalogue->url,
                        ['class' => 'thumbnail']
                ) ?>
            </div>
        <?php endif; ?>
        <?php if ($recomendations && $recomendations->visibility): ?>
            <div class="col-md-4">
                <?= Html::a(
                        Html::img($recomendations->thumbUrl),
                        $recomendations->url,
                        ['class' => 'thumbnail']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($purchase && $purchase->visibility): ?>
        <div id="main-cat-level-2-purch" class="main-cat-level-2" style="display: none;">
            <?php $purchases = Category::getMenuItems($purchase); ?>
            <?php if ($purchases): ?>
                <?php foreach ($purchases as $cat): ?>
                    <div class="col-md-4">
                        <?= Html::a(
                                Html::img($cat['thumbUrl']),
                                $cat['url'],
                                ['class' => 'thumbnail']
                        ) ?>
                        <h5 class="text-center"><?= $cat['content'] ?></h5>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($catalogue && $catalogue->visibility): ?>
        <div id="main-cat-level-2-catal" class="main-cat-level-2" style="display: none;">
            <?php $catalogues = Category::getMenuItems($catalogue); ?>
            <?php if ($catalogues): ?>
                <?php foreach ($catalogues as $cat): ?>
                    <div class="col-md-4">
                        <?= Html::a(
                                Html::img($cat['thumbUrl']),
                                $cat['url'],
                                ['class' => 'thumbnail']
                        ) ?>
                        <h5 class="text-center"><?= $cat['content'] ?></h5>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($recomendations && $recomendations->visibility): ?>
        <div id="main-cat-level-2-recom" class="main-cat-level-2" style="display: none;">
            <?php $recomendations_a = Category::getMenuItems($recomendations); ?>
            <?php if ($recomendations_a): ?>
                <?php foreach ($recomendations_a as $cat): ?>
                    <div class="col-md-4">
                        <?= Html::a(
                                Html::img($cat['thumbUrl']),
                                $cat['url'],
                                ['class' => 'thumbnail']
                        ) ?>
                        <h5 class="text-center"><?= $cat['content'] ?></h5>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>