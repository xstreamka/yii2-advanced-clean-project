<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
            'enableCsrfValidation' => true,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'yii2-advanced',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\debug\Module*',
                    ],
                ],
                // Фиксирование ошибок 404 на почту.
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' => 'mailer',
                    'levels' => ['error'],
                    'categories' => [
                        'yii\web\HttpException:404',
                    ],
                    'message' => [
                        'from' => $params['errorEmail'],
                        'to' => $params['supportEmail'],
                        'subject' => "{$params['url']}{$_SERVER['REQUEST_URI']} Ошибка 404 на сайте {$params['host']}",
                    ],
                ],
                // Фиксирование остальных ошибок на почту.
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' => 'mailer',
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'message' => [
                        'from' => $params['errorEmail'],
                        'to' => $params['supportEmail'],
                        'subject' => "{$params['url']}{$_SERVER['REQUEST_URI']} Ошибка 50* на сайте {$params['host']}",
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '/',
            'rules' => [
                // Базовые правила.
                '' => 'site/index',
                '<action:(login|logout)>' => 'site/<action>',

                // Модуль debug.
                'debug/<controller>/<action>' => 'debug/<controller>/<action>',

                // Все остальное.
                '<controller>/<action>/<id:[\d]+>' => '<controller>/<action>',
                '<controller>/<action>/<alias:[\w-]+>' => '<controller>/<action>',
                '<controller>/<action>' => '<controller>/<action>',
                '<controller>' => '<controller>/index',
            ]
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
    ],
    'params' => $params,
];
