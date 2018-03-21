<?php
use yii\helpers\Url;
use yii\bootstrap\Nav;
use kartik\helpers\Html;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => [
        [
            'label' => Icon::show('shopping-cart') . ' Корзина ' . Html::badge($cart->information, ['class' => 'cart-information']),
            'url' => Url::to(['/cart']),
        ],
        [
            'label' => Icon::show('credit-card') . ' Счет ' . Html::badge(Yii::$app->user->identity->entity->deposit->total),
            'url' => Url::to(['/profile/account']),
        ],
        [
            'label' => Icon::show('info-circle') . ' Информация',
            'items' => [
                [
                    'label' => Icon::show('list') . ' Прайс-лист',
                    'url' => Url::to(['/pricelist/product']),
                ],
                [
                    'label' => Icon::show('rouble') . ' Оплата',
                    'url' => Url::to(['/page/oplata']),
                ],
                [
                    'label' => Icon::show('gift') . ' Доставка',
                    'url' => Url::to(['/page/dostavka']),
                ],
            ],
        ],
        [
            'label' => Icon::show('user') . ' ' . Html::encode(Yii::$app->user->identity->entity->shortName),
            'url' => Url::to(['/profile']),
            'items' => [
                [
                    'label' => Icon::show('list-alt') . ' Мои заказы',
                    'url' => Url::to(['/profile/partner/order']),
                ],
                [
                    'label' => Icon::show('users') . ' Мои участники',
                    'url' => Url::to(['/profile/partner/member']),
                ],
                /*[
                    'label' => Icon::show('bars') . ' Заказы моих участников',
                    'url' => Url::to(['/profile/partner/member/order']),
                ],*/
                [
                    'label' => Icon::show('bars') . ' Добавить заказ',
                    'url' => Url::to(['profile/partner/member/order-create']),
                ],
                [
                    'label' => Icon::show('bars') . ' Коллективная закупка',
                    'url' => Url::to(['/profile/provider/order/index']),
                ],
                [
                    'label' => Icon::show('bars') . ' Заказы на склад',
                    'url' => Url::to(['/profile/partner/order/index']),
                ],
                [
                    'label' => Icon::show('briefcase') . ' Мои услуги',
                    'url' => Url::to(['/profile/service']),
                ],
                [
                    'label' => Icon::show('pencil-square-o') . ' Личные данные',
                    'url' => Url::to(['/profile/partner/personal']),
                ],
                [
                    'label'=> Icon::show('search') . 'Поиск контрагентов',
                    'url' => '#',
                    'options' => ['data-toggle' => 'modal', 'data-target'=>'#myModal'],
                ],
                [
                    'label' => Icon::show('sign-out') . ' Выход',
                    'url' => Url::to(['/profile/logout']),
                    
                ],
            ],
        ],
    ],
    'encodeLabels' => false,
]);

