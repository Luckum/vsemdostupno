<?php

use yii\helpers\Html;

?>

<?php if ($categories): ?>
    <div class="row category-panel">
        <div class="col-md-12">
            <?php if (!empty($name)): ?>
                <div class="row category-name">
                    <div class="col-md-12">
                        <h2><?= Html::encode($name) ?></h2>
                    </div>
                </div>
            <?php endif ?>
            <div class="row">
                <div class="col-md-12">
                    <?= $this->renderFile('@app/modules/site/views/category/snippets/category-grid.php', [
                        'categories' => $categories,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
