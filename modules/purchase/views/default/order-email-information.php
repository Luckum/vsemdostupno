<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<ol>
<?php foreach ($model->purchaseOrderProducts as $k => $orderHasProduct): ?>
    <li>
        <?php if ($orderHasProduct->product): ?>
            <?= Html::a(Html::encode($orderHasProduct->name . ', ' . $orderHasProduct->productFeature->featureName), Url::to([$orderHasProduct->product->url], true), ['target' => '_blank']) ?>
                (<?= $orderHasProduct->purchaseProduct->htmlFormattedPurchaseDate ?>)
        <?php else: ?>
            <?= Html::encode($orderHasProduct->name) ?>
        <?php endif ?>
        <?= Html::encode($orderHasProduct->quantity . ' x ' . Yii::$app->formatter->asCurrency($orderHasProduct->price, 'RUB') . ' = ' . Yii::$app->formatter->asCurrency($orderHasProduct->total, 'RUB')) ?>
    </li>
<?php endforeach ?>
</ol>