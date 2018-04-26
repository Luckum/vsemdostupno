<ol>
<?php foreach ($model->purchaseOrderProducts as $k => $orderHasProduct): ?>
    <?php if ($orderHasProduct->purchaseProduct->purchase_date == $date): ?>
        <li>
            <?php if ($orderHasProduct->product): ?>
                <a href="<?= 'http://vsemdostupno.ru' . $orderHasProduct->product->url ?>" target="_blank"><?= $orderHasProduct->name . ', ' . $orderHasProduct->productFeature->featureName ?></a>
            <?php else: ?>
                <?= $orderHasProduct->name ?>
            <?php endif ?>
            <?= $orderHasProduct->quantity . ' x ' . Yii::$app->formatter->asCurrency($orderHasProduct->price, 'RUB') . ' = ' . Yii::$app->formatter->asCurrency($orderHasProduct->total, 'RUB') ?>
        </li>
    <?php endif; ?>
<?php endforeach ?>
</ol>