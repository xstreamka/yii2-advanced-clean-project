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
//		'rbac' => [
//			'class' => 'yii2mod\rbac\Module',
//		],
		'gii' => [
			'class' => 'yii\gii\Module',
			'allowedIPs' => [] // adjust this to your needs
		],
		'debug' => [
			'class' => 'yii\debug\Module',
			'traceLine' => '<a href="phpstorm://open?url=file://{file}&line={line}">{file}:{line}</a>',
            'allowedIPs' => [],
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
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true],
        ],
//        'session' => [
//            // this is the name of the session cookie used for login on the backend
//            'name' => 'advanced-backend',
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
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
		        '' => 'site/index',
		        '<action:(login|logout)>' => 'site/<action>',
		        '<controller>/<action>/<id:[\d]+>' => '<controller>/<action>',
		        '<controller>/<action>/<alias:[\d\-_a-zA-Z]+>' => '<controller>/<action>',
		        '<module>/<controller>/<action>/<id:[\d]+>' => '<module>/<controller>/<action>',
		        '<module>/<controller>/<action>' => '<module>/<controller>/<action>',
		        '<controller>/' => '<controller>/index',
		        '<controller>/<action>' => '<controller>/<action>',
	        ],
        ],
	    'assetManager' => [
		    'basePath' => '@webroot/assets',
		    'baseUrl' => '@web/assets',
		    'linkAssets' => true,
		    'appendTimestamp' => true,
	    ],
	    'authManager' => [
		    'class' => 'yii\rbac\DbManager',
	    ],
    ],
    'on beforeAction' => function (\yii\base\ActionEvent $event) {
        // Панель Yii Debug.
        if (Yii::$app->user->can('yii_debug')) {
            Yii::$app->getModule('debug')->allowedIPs = ['*'];
        }
    },
    'params' => $params,
];
