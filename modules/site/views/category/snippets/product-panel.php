<?php

use yii\helpers\Html;

?>

<?php if ($products): ?>
    <div class="row product-panel" id="inner-product">
        <div class="col-md-12">
            <?php if (!empty($name)): ?>
                <div class="row product-name">
                    <div class="col-md-12">
                        <h2><?= Html::encode($name) ?></h2>
                    </div>
                </div>
            <?php endif ?>
            <div class="row">
                <div class="col-md-12">
                    <?= $this->renderFile('@app/modules/site/views/category/snippets/product-grid.php', [
                        'products' => $products,
                        'pages' => isset($pages) ? $pages : null,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
