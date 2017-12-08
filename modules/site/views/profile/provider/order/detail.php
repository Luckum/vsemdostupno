<?php
use kartik\helpers\Html;
use yii\helpers\Url;

$this->title = 'Детали заказа';
$this->params['breadcrumbs'][] = ['label' => 'Заказы поставщикам', 'url' => '/profile/provider/order/index'];
$this->params['breadcrumbs'][] = $this->title;
$total_price = $total_qnt = 0;
?>

<?= Html::pageHeader(Html::encode($this->title)) ?>
<h4>Заявка от участников <?= $partner->name; ?> для поставки товаров на <?= date('d.m.Y', strtotime($date)); ?></h4>
<div class="order-index">
    <table class="table table-bordered">
        <thead>
            <th>Поставщик</th>
            <th>Наименование товаров</th>
            <th>Ед. измерения</th>
            <th>№ п/п</th>
            <th>Ф.И.О. участников заказавших товар</th>
            <th>№ заявки</th>
            <th>Цена за ед. товара</th>
            <th>Количество</th>
            <th>На сумму</th>
        </thead>
        <tbody>
            <?php $rowspan = count($details); ?>
            <?php if ($rowspan == 1): ?>
                <tr>
                    <td><?= $provider->name; ?></td>
                    <td><?= $product->name; ?></td>
                    <td><?= $product->packing; ?></td>
                    <td><?= 1; ?></td>
                    <td><?= $details[0]['fio']; ?></td>
                    <td><?= $details[0]['id']; ?></td>
                    <td><?= $details[0]['price']; ?></td>
                    <td><?= number_format($details[0]['quantity']); ?></td>
                    <td><b><?= $details[0]['total']; ?></b></td>
                </tr>
                <?php $total_price += $details[0]['total']; ?>
                <?php $total_qnt += $details[0]['quantity']; ?>
            <?php else: ?>
                <tr>
                    <td rowspan="<?= $rowspan; ?>" class="td-v-align"><?= $provider->name; ?></td>
                    <td rowspan="<?= $rowspan; ?>" class="td-v-align"><?= $product->name; ?></td>
                    <td rowspan="<?= $rowspan; ?>" class="td-v-align"><?= $product->packing; ?></td>
                    <td><?= 1; ?></td>
                    <td><?= $details[0]['fio']; ?></td>
                    <td><?= $details[0]['id']; ?></td>
                    <td><?= $details[0]['price']; ?></td>
                    <td><?= number_format($details[0]['quantity']); ?></td>
                    <td><b><?= $details[0]['total']; ?></b></td>
                </tr>
                <?php $total_price += $details[0]['total']; ?>
                <?php $total_qnt += $details[0]['quantity']; ?>
                <?php foreach ($details as $k => $detail): ?>
                    <?php if ($k != 0): ?>
                        <tr>
                            <td><?= $k + 1 ?></td>
                            <td><?= $detail['fio']; ?></td>
                            <td><?= $detail['id']; ?></td>
                            <td><?= $detail['price']; ?></td>
                            <td><?= number_format($detail['quantity']); ?></td>
                            <td><b><?= $detail['total']; ?></b></td>
                        </tr>
                        <?php $total_price += $detail['total']; ?>
                        <?php $total_qnt += $detail['quantity']; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <td colspan="7"><b>ИТОГО:</b></td>
            <td><?= number_format($total_qnt); ?></td>
            <td><b><?= number_format($total_price, 2, ".", ""); ?></b></td>
        </tfoot>
    </table>
</div>