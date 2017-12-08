<?php

use yii\widgets\LinkPager;
use kartik\helpers\Html;

?>

<?php if ($products): ?>
    <div class="product-grid">
        <?php if (isset($pages)): ?>
            <div class="row text-right">
                <div class="col-md-12">
                    <?= LinkPager::widget([
                        'pagination' => $pages,
                    ]) ?>
                </div>
            </div>
        <?php endif ?>

        <?php for ($exCount = 0; $exCount < count($products); $exCount += 4): ?>
            <div class="row text-center">
                <?php for ($inCount = $exCount; $inCount < $exCount + 4 && $inCount < count($products); $inCount += 1): ?>
                    <div class="col-md-3 product-item">
                        <div class="row">
                            <div class="col-md-12">
                                <?= Html::a(
                                    Html::img($products[$inCount]->thumbUrl),
                                    $products[$inCount]->url,
                                    ['class' => 'thumbnail']
                                ) ?>
                            </div>
                        </div>
                        <div class="row product-name">
                            <div class="col-md-12">
                                <?= Html::tag('h5', Html::encode($products[$inCount]->name)) ?>
                            </div>
                        </div>
                        <div class="row product-price">
                            <div class="col-md-12">
                                <?php if (Yii::$app->user->isGuest): ?>
                                    <?= Html::badge($products[$inCount]->formattedMemberPrice, ['class' => '']) ?>
                                <?php else: ?>
                                    <?= Html::badge($products[$inCount]->formattedCalculatedPrice, ['class' => '']) ?>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endfor ?>
            </div>
        <?php endfor ?>

        <?php if (isset($pages)): ?>
            <div class="row text-right">
                <div class="col-md-12">
                    <?= LinkPager::widget([
                        'pagination' => $pages,
                    ]) ?>
                </div>
            </div>
        <?php endif ?>
    </div>
<?php endif ?>
