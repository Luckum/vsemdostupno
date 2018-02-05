<?php
use yii\helpers\Url;
use yii\bootstrap\Nav;
use kartik\helpers\Html;
use kartik\icons\Icon;
use yii\bootstrap\Modal;

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
            'options' => ['id' => 'user-menu-lnk'],
            'items' => [
                [
                    'label' => Icon::show('list-alt') . ' Мои заказы',
                    'url' => Url::to(['/profile/member/order']),
                ],
                [
                    'label' => Icon::show('briefcase') . ' Мои услуги',
                    'url' => Url::to(['/profile/service']),
                ],
                [
                    'label'=> Icon::show('product-hunt') . 'Мои товары',
                    'url'=>'javascript:void(0)',
                    'options' => ['data-toggle' => 'modal', 'data-target' => '#providerModal'],
                ],
                [
                    'label' => Icon::show('pencil-square-o') . ' Личные данные',
                    'url' => Url::to(['/profile/member/personal']),
                ],
                [
                    'label' => Icon::show('envelope-o') . 'Настройка рассылки',
                    'url' => 'javascript:void(0)',
                    'visible' => Yii::$app->hasModule('mailing'),
                    'options' => ['data-toggle' => 'modal', 'data-target' => '#mailing-settings'],
                ],
                [
                    'label' => Icon::show('check-square-o') . 'Проголосуйте',
                    'url' => Url::to(['/mailing/vote']),
                    'visible' => Yii::$app->hasModule('mailing'),
                    'options' => ['id' => 'vote-menu-lnk'],
                ],
                [
                    'label' => Icon::show('comments') . 'Жалобы, предложения',
                    'url' => Url::to(['/mailing/message']),
                    'visible' => Yii::$app->hasModule('mailing'),
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
