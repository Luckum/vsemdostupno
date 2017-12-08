<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
$this->title = '';

$panels = [
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
}
?>
