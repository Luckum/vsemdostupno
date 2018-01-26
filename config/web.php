<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'charset' => 'UTF-8',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'app\components\ModuleManager'
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'PGl5g0Qfp-5pd6hiZf8tJYD5d5prKiTn',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\site\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['profile/login'],
            'on ' . yii\web\User::EVENT_AFTER_LOGIN => ['app\modules\site\models\User', 'handleAfterLogin'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/default/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'info@vsemdostupno.ru',
                'password' => 'EeTh1Uij',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'formatter' => [
            'defaultTimeZone' => 'Europe/Moscow',
        ],
        'urlManager' => require(__DIR__ . '/urlManager.php'),
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '6Lc1IBMTAAAAAPZnX-2z8X9eQm5mVYu_sB6KG93n',
            'secret' => '6Lc1IBMTAAAAANGZcCq4fzLV9K_waMq2d2ydC-cv',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'ru-RU',
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'modules' => [
        'site' => [
            'class' => 'app\modules\site\Module',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
     ],
    'controllerMap' => [
        'elfinder' => [
            'class' => 'app\modules\site\controllers\ElfinderController',
            'access' => ['admin', 'superadmin'],
            'root' => [
                'baseUrl' => '@web',
                'basePath'=>'@webroot',
                'path' => '',
                'name' => 'Файлы'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
