<?php
use yii\helpers\Url;
use yii\bootstrap\Nav;
use kartik\helpers\Html;
use kartik\icons\Icon;

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
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
        [
            'label' => Icon::show('user') . ' ' . Html::encode(Yii::$app->user->identity->entity->shortName),
            'url' => Url::to(['/profile']),
            'items' => [
                [
                    'label' => Icon::show('cogs') . ' Панель управления',
                    'url' => Url::to(['/admin']),
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
