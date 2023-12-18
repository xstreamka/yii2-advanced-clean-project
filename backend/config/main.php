<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
            'as access' => [
                'class' => \yii2mod\rbac\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['superadmin'],
                    ],
                ],
            ],
        ],
        'user' => [
            'class' => 'backend\modules\user\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin',
            'enableCsrfValidation' => true,
        ],
        'user' => [
            'class' => 'common\components\UserComponent',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-yii2', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'yii2-advanced',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
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

                // Точка входа модуля.
                'user/<action:(view|update|delete)>/<id:[\d]+>' => 'user/user/<action>',
                'user/<action:(create)>' => 'user/user/<action>',
                'user' => 'user/user/index',
                // Остальная обработка модуля.
                '<module:(user|rbac)>/<controller:[\w-]+>/<action:[\w-]+>/<id:[\d]+>' => '<module>/<controller>/<action>',
                '<module:(user|rbac)>/<controller:[\w-]+>/<action:[\w-]+>/<alias:[\w-]+>' => '<module>/<controller>/<action>',
                '<module:(user|rbac)>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',
                '<module:(user|rbac)>/<controller:[\w-]+>' => '<module>/<controller>/index',

                // Модуль debug.
                'debug/<controller>/<action>' => 'debug/<controller>/<action>',

                // Все остальное.
                '<controller>/<action>/<id:[\d]+>' => '<controller>/<action>',
                '<controller>/<action>/<alias:[\w-]+>' => '<controller>/<action>',
                '<controller>/<action>' => '<controller>/<action>',
                '<controller>' => '<controller>/index',
            ],
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
    ],
    'on beforeAction' => function (\yii\base\ActionEvent $event) {
        $user = Yii::$app->user;
        // Глобальная проверка на права админки.
        if (!$user->isGuest && !$user->can('dashboard')) {
            Yii::$app->response->redirect('/');
            Yii::$app->end();
        }
        // Очистка всего кеша.
        if (
            Yii::$app->request->get('clear_cache')
            && $user->can('clear_cache')
        ) {
            Yii::$app->cache->flush();
            Yii::$app->cacheFrontend->flush();
            Yii::$app->response->redirect(\common\helpers\CF::getCleanUrl());
            Yii::$app->session->setFlash('success', 'Кэш успешно очищен');
            Yii::$app->end();
        }
    },
    'container' => [
        'definitions' => [
            \yii\widgets\LinkPager::class => [
                'class' => \yii\bootstrap5\LinkPager::class,
            ],
        ],
    ],
    'params' => $params,
];
