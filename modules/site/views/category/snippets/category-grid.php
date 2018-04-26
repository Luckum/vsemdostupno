<?php

use yii\helpers\Html;

use app\modules\purchase\models\PurchaseProduct;
?>
<?php if ($categories): ?>
    <div class="category-grid">
        <?php for ($exCount = 0; $exCount < count($categories); $exCount += 4): ?>
            <div class="row category-item">
            <?php for ($inCount = $exCount; $inCount < $exCount + 4 && $inCount < count($categories); $inCount += 1): ?>
                <?php if ($categories[$inCount]->isPurchase()): ?>
                    <?php $productsQuery = $categories[$inCount]->getAllProductsQuery()
                            ->andWhere('visibility != 0')
                            ->andWhere('published != 0'); 
                        $products = $productsQuery->all();
                        $date = PurchaseProduct::getClosestDate($products);
                    ?>
                <?php endif; ?>
                <div class="col-md-3">
                    <?php if ($categories[$inCount]->isPurchase()): ?>
                        <div style="height: 25px;">
                            <h5 class="text-center" style="font-size: 20px;"><strong><?= $date ? 'Закупка ' . date('d.m.Yг.', strtotime($date)) : '' ?></strong></h5>
                        </div>
                    <?php endif; ?>
                    <?= Html::a(
                        Html::img($categories[$inCount]->thumbUrl),
                        $categories[$inCount]->url,
                        ['class' => 'thumbnail']
                    ) ?>
                    <?= Html::tag('h5', $categories[$inCount]->htmlFormattedFullName, ['class' => 'text-center']) ?>
                </div>
            <?php endfor ?>
            </div>
        <?php endfor ?>
    </div>
<?php endif ?>
