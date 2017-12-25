<?php

use yii\web\View;
use yii\bootstrap\Alert;
use yii\web\JsExpression;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\icons\Icon;
use dosamigos\selectize\SelectizeDropDownList;
use app\models\Parameter;
use app\models\Cart;

/* @var $this yii\web\View */
$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<div class="cart">
    <?php if ($model->isEmpty()): ?>
        <div class="row">
            <div class="col-md-12">
                <?= Alert::widget([
                    'body' => 'Пока пусто.',
                    'options' => [
                        'class' => 'alert-info',
                    ],
                ])?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Товары</th>
                            <th class="col-md-2 text-center">Количество</th>
                            <th class="col-md-2 text-center">Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->products as $product): ?>
                            <tr>
                                <td class="row">
                                    <div class="col-md-2">
                                        <?= Html::a(
                                            Html::img($product->product->thumbUrl),
                                            $product->product->url,
                                            ['class' => 'thumbnail']
                                        ) ?>
                                    </div>
                                    <div class="col-md-10">
                                        <p>
                                            <?= Html::a(Html::encode($product->product->name), $product->product->url) ?>
                                        </p>
                                        <p>
                                            <?= $product->tare . ', ' . $product->volume . ' ' . $product->measurement; ?>
                                            <?= Html::badge(Html::encode($product->formattedCalculatedPrice)) ?>
                                        </p>
                                        <?php if ($product->product->isPurchase()): ?>
                                            <p>
                                                <b>Закупка:</b>
                                                <?= Html::a(
                                                    $product->product->purchaseCategory->htmlFormattedPurchaseDate,
                                                    $product->product->purchaseCategory->url
                                                ) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?= SelectizeDropDownList::widget([
                                        'name' => 'quantity',
                                        'value' => $product->cart_quantity,
                                        'items' => range(0, $product->quantity),
                                        'options' => [
                                            'data-product-id' => $product->id,
                                            'readonly' => true,
                                            'onchange' => new JsExpression('
                                                var id = $(this).attr("data-product-id");
                                                var quantity = $(this).val();

                                                $(this).prop("disabled", true);
                                                WidgetHelpers.showLoading();

                                                if (CartHelpers.update(id, quantity)) {
                                                    WidgetHelpers.hideLoading();
                                                    $(".cart-information").text(CartHelpers.Information);
                                                    $("td[data-product-id=\"" + id + "\"]").text(CartHelpers.UpdatedProductInformation);
                                                    $(this)[0].selectize.setValue(CartHelpers.UpdatedProductQuantity, true);
                                                    $(".cart button.order").prop("disabled", !CartHelpers.Order);
                                                    $(".cart button.clear").prop("disabled", !CartHelpers.Order);
                                                } else {
                                                    WidgetHelpers.hideLoading();
                                                    WidgetHelpers.showFlashDialog(CartHelpers.Message);
                                                }

                                                $(this).prop("disabled", false);

                                                return false;
                                            '),
                                        ],
                                    ]) ?>
                                </td>
                                <td class="text-center" data-product-id="<?= $product->id ?>"><?= Html::encode($product->formattedCalculatedTotalPrice) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row text-right total">
            <div class="col-md-12">
                <?= Html::tag('b', 'Итого: ' . Html::tag('span', Html::encode($model->formattedTotal), ['class' => 'cart-information'])) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= Html::button(Icon::show('trash') . ' Очистить', [
                    'class' => 'btn btn-danger clear',
                    'onclick' => new JsExpression('
                        $(this).prop("disabled", true);

                        yii.confirm("Очистить содержимое корзины?", function () {
                            WidgetHelpers.showLoading();
                            if (CartHelpers.clear()) {
                                location.reload();
                            } else {
                                WidgetHelpers.hideLoading();
                                WidgetHelpers.showFlashDialog(CartHelpers.Message);
                            }
                        });

                        $(this).prop("disabled", false);

                        return false;
                    '),
                ]) ?>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4 text-right">
                <?= Html::button(Icon::show('check') . ' Оформить заказ', [
                    'class' => 'btn btn-success order',
                    'onclick' => new JsExpression('
                        $(this).prop("disabled", true);
                        window.location.href = "' . Url::to(['/cart/order']) . '";

                        return false;
                    '),
                ]) ?>
            </div>
        </div>
    <?php endif ?>
</div>
