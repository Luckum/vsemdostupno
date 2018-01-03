<?php

use yii\web\View;
use yii\bootstrap\Alert;
use yii\web\JsExpression;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\icons\Icon;
use app\models\Category;
use app\models\Parameter;
use app\models\Cart;
use app\models\User;
use app\models\Member;
use dosamigos\gallery\Gallery;
use dosamigos\selectize\SelectizeDropDownList;

/* @var $this yii\web\View */
$this->title = $model->name;

$url = Yii::$app->request->referrer;
if (preg_match('/\/category\/\d+$/', $url)) {
    $categoryId = preg_replace('/^\D+/', '', $url);
    $category = Category::find()
        ->where('visibility != 0 AND id = :id', [':id' => $categoryId])
        ->one();
    if ($category) {
        $this->params['breadcrumbs'] = $category->getBreadcrumbs($model->name);
    }
} else {
    $this->params['breadcrumbs'] = [];
    $this->params['breadcrumbs'][] = $model->name;
    //$category = $model->purchaseCategory;
}

$productImages = [];
foreach ($model->productHasPhoto as $productHasPhoto) {
    $productImages[] = [
        'url' => $productHasPhoto->imageUrl,
        'src' => $model->thumbUrl,
        'options' => ['class' => 'hidden'],
    ];
}

$enableCart = false;
if (Yii::$app->user->isGuest) {
    $enableCart = true;
} else {
    if (!in_array(Yii::$app->user->identity->role, [User::ROLE_ADMIN, User::ROLE_PROVIDER])) {
        $enableCart = true;
    }
    if (Yii::$app->user->identity->role == User::ROLE_PROVIDER) {
        $member = Member::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        if($member) {
            $enableCart = true;
        }
    }
}

$features = [];
foreach ($model->productFeatures as $feat) {
    if ($feat->quantity > 0) {
        $features[$feat->id] = $feat->tare . ', ' . $feat->volume . ' ' . $feat->measurement;
    }
}
?>

<?= Html::pageHeader(Html::encode($model->name)) ?>

<div class="row">
    <div class="col-md-6">
        <?= Gallery::widget(['id' => 'product-images', 'items' => $productImages]) ?>
        <?= Html::a(
                Html::img($model->thumbUrl),
                '#',
                [
                    'class' => 'thumbnail',
                    'onclick' => new JsExpression('
                        $("#product-images a").first().trigger("click");
                        return false;
                    '),
                ]
        ) ?>
    </div>
    <div class="col-md-6">
        <?php if ($enableCart): ?>
            <div class="row add-product-to-cart-panel">
                <div class="col-md-5">
                    <?= SelectizeDropDownList::widget([
                        'name' => 'feature',
                        'items' => $features,
                        'options' => [
                            'readonly' => true,
                            'onchange' => new JsExpression('
                                $(".qnt-container").each(function() {
                                    $(this).hide();
                                });
                                $("#quantity-container-"+$(this).val()).show();
                                var html = $.ajax({
                                    url: "/site/product/get-prices",
                                    async: false,
                                    type: "POST",
                                    data: {f_id: $(this).val()}
                                }).responseText;
                                if (html) {
                                    $("#prices-container").html(html);
                                }
                                $.ajax({
                                    url: "/site/product/in-cart",
                                    type: "POST",
                                    data: {f_id: $(this).val()},
                                    success: function(response) {
                                        if (response) {
                                            $("#cart-btn").addClass("btn-product-in-cart");
                                            $("#cart-btn").removeClass("btn-success");
                                            $("#cart-btn").addClass("btn-info");
                                            $("#cart-btn").html(\'' . Icon::show('shopping-cart') . ' Товар в корзине\');
                                        } else {
                                            $("#cart-btn").removeClass("btn-product-in-cart");
                                            $("#cart-btn").removeClass("btn-info");
                                            $("#cart-btn").addClass("btn-success");
                                            $("#cart-btn").html(\'' . Icon::show('cart-plus') . ' Добавить в корзину\');
                                        }
                                    }
                                });
                            '),
                        ],
                    ]) ?>
                </div>
                <?php $cnt_show = 1; ?>
                <?php foreach ($model->productFeatures as $k => $feat): ?>
                    <?php if ($feat->quantity > 0): ?>
                        <div class="col-md-3 qnt-container" data-feature-id="<?= $feat->id; ?>" id="quantity-container-<?= $feat->id; ?>" <?php if ($cnt_show != 1): ?>style="display: none;"<?php endif; ?>>
                            <?= SelectizeDropDownList::widget([
                                'name' => 'quantity',
                                'value' => Cart::hasQuantity($feat),
                                'items' => array_combine(
                                    range(1, $feat->quantity),
                                    range(1, $feat->quantity)
                                ),
                                'options' => [
                                    'data-product-id' => $feat->id,
                                    'id' => $feat->id,
                                    'readonly' => true,
                                    'onchange' => new JsExpression('
                                        if ($(".btn-product-in-cart").length) {
                                            var id = $(this).attr("data-product-id");
                                            var quantity = $(this).val();

                                            $(this).prop("disabled", true);
                                            WidgetHelpers.showLoading();

                                            if (CartHelpers.update(id, quantity)) {
                                                WidgetHelpers.hideLoading();
                                                WidgetHelpers.showFlashDialog(CartHelpers.Message);
                                                $(this)[0].selectize.setValue(CartHelpers.UpdatedProductQuantity, true);
                                            } else {
                                                WidgetHelpers.hideLoading();
                                                WidgetHelpers.showFlashDialog(CartHelpers.Message);
                                                $(this).removeClass("btn-product-in-cart");
                                                $(this).removeClass("btn-info");
                                                $(this).addClass("btn-success");
                                                $(this).html(\'' . Icon::show('cart-plus') . ' Добавить в корзину\');
                                            }

                                            if (CartHelpers.Information) {
                                                $(".cart-information").text(CartHelpers.Information);
                                            }

                                            $(this).prop("disabled", false);
                                        }

                                        return false;
                                    '),
                                ],
                            ]) ?>
                        </div>
                        <?php $cnt_show = 0; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="col-md-6">
                    <?php
                        if (Cart::hasProduct($model)) {
                            $icon = 'shopping-cart';
                            $title = 'Товар в корзине';
                            $class = 'btn-product-in-cart btn-info';
                        } else {
                            $icon = 'cart-plus';
                            $title = 'Добавить в корзину';
                            $class = 'btn-success';
                        }
                        echo Html::button(Icon::show($icon) . ' ' . $title, [
                            'class' => 'btn ' . $class,
                            'id' => 'cart-btn',
                            'onclick' => new JsExpression('
                                var obj = $(".qnt-container:visible");
                                var feature = $(obj).attr("data-feature-id");
                                var quantity = $("#"+feature).val();

                                $(this).prop("disabled", true);
                                
                                if ($(this).hasClass("btn-info")) {
                                    window.location.href = "' . Url::to(['/cart']) . '";
                                    return false;
                                } else {
                                    WidgetHelpers.showLoading();
                                }
                                
                                
                                
                                if (CartHelpers.add(feature, quantity)) {
                                    WidgetHelpers.hideLoading();
                                    WidgetHelpers.showFlashDialog(CartHelpers.Message);
                                    $(this).addClass("btn-product-in-cart");
                                    $(this).removeClass("btn-success");
                                    $(this).addClass("btn-info");
                                    $(this).html(\'' . Icon::show('shopping-cart') . ' Товар в корзине\');
                                } else {
                                    WidgetHelpers.hideLoading();
                                    WidgetHelpers.showFlashDialog(CartHelpers.Message);
                                }

                                if (CartHelpers.Information) {
                                    $(".cart-information").text(CartHelpers.Information);
                                }

                                $(this).prop("disabled", false);

                                return false;
                            '),
                        ]);
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12" id="prices-container">
                <?php
                    $prices = [
                        [
                            'content' => 'Стоимость для всех желающих',
                            'badge' => $model->formattedPrice,
                            'options' => ['class' => !Yii::$app->user->isGuest ? 'disabled' : ''],
                        ],
                        [
                            'content' => 'Стоимость для участников ПО',
                            'badge' => $model->formattedMemberPrice,
                            'options' => ['class' => Yii::$app->user->isGuest ? 'disabled' : ''],
                        ],
                    ];

                    echo Html::panel([
                            'heading' => Icon::show('tags') . ' Стоимость',
                            'postBody' => Html::listGroup($prices),
                            'headingTitle' => true,
                        ],
                        Html::TYPE_PRIMARY) ?>
            </div>
        </div>
        <?php if ($model->isPurchase()): ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                        if ($model->purchaseCategory->formattedOrderDate) {
                            echo Alert::widget([
                                'body' => sprintf(
                                    'Закупка состоится %s, заказы принимаются до %s включительно.',
                                    Html::a($model->purchaseCategory->htmlFormattedPurchaseDate, Url::to([$model->purchaseCategory->url])),
                                    Html::a($model->purchaseCategory->htmlFormattedOrderDate, Url::to([$model->purchaseCategory->url]))
                                ),
                                'options' => [
                                    'class' => 'alert-info alert-def',
                                ],
                            ]);
                        } else {
                            echo Alert::widget([
                                'body' => sprintf(
                                    'Закупка состоится %s',
                                    Html::a($model->purchaseCategory->htmlFormattedPurchaseDate, Url::to([$model->purchaseCategory->url]))
                                ),
                                'options' => [
                                    'class' => 'alert-info alert-def',
                                ],
                            ]);
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= Alert::widget([
                        'body' => Parameter::getValueByName('purchase-info'),
                        'options' => [
                            'class' => 'alert-info alert-def',
                        ],
                    ])?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="product-description">
    <?php
        $attributes = [
            'composition',
            'packing',
            'manufacturer',
            'status',
        ];
    ?>
    <?php foreach ($attributes as $attribute): ?>
        <?php if ($model->$attribute): ?>
            <div class="row">
                <div class="col-md-12">
                    <?= Html::tag('b', $model->getAttributeLabel($attribute) . ':') ?> <?= Html::encode($model->$attribute) ?>
                </div>
            </div>
        <?php endif ?>
    <?php endforeach ?>

    <div class="row">
        <div class="col-md-12">
            <?= Html::tag('h2', 'Описание') ?>
            <?= $model->description ?>
        </div>
    </div>
</div>
