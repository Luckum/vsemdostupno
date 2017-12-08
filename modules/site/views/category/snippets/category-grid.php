<?php

use yii\helpers\Html;

?>
<?php if ($categories): ?>
    <div class="category-grid">
        <?php for ($exCount = 0; $exCount < count($categories); $exCount += 4): ?>
            <div class="row category-item">
            <?php for ($inCount = $exCount; $inCount < $exCount + 4 && $inCount < count($categories); $inCount += 1): ?>
                <div class="col-md-3">
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
