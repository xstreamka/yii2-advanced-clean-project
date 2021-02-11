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
		'user' => [
			'class' => 'common\components\UserComponent',
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity', 'httpOnly' => true],
		],
//		'session' => [
//			// this is the name of the session cookie used for login on the frontend
//			'name' => 'advanced-frontend',
//		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
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
						'from' => 'error404@' . $params['host'],
						'to' => $params['supportEmail'],
						'subject' => 'http' . (($_SERVER['HTTPS'] ?? '') == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' Ошибка 404 на сайте ' . $params['host'],
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
						'from' => 'error500@' . $params['host'],
						'to' => $params['supportEmail'],
						'subject' => 'http' . (($_SERVER['HTTPS'] ?? '') == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' Ошибка 50* на сайте ' . $params['host'],
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
				'' => 'site/index',
				'<action:(login|logout)>' => 'site/<action>',
				'<controller>/<action>/<id:[\d]+>' => '<controller>/<action>',
				'<controller>/<action>/<alias:[\d\-_a-zA-Z]+>' => '<controller>/<action>',
				'<module>/<controller>/<action>/<id:[\d]+>' => '<module>/<controller>/<action>',
				'<module>/<controller>/<action>' => '<module>/<controller>/<action>',
				'<controller>/' => '<controller>/index',
				'<controller>/<action>' => '<controller>/<action>',
			]
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
	'modules' => [
		'debug' => [
			'class' => 'yii\debug\Module',
			'traceLine' => '<a href="phpstorm://open?url=file://{file}&line={line}">{file}:{line}</a>',
			'allowedIPs' => []
		],
	],
	'on beforeAction' => function (\yii\base\ActionEvent $event) {
		// Панель Yii Debug.
		if (Yii::$app->user->can('yii_debug')) {
			Yii::$app->getModule('debug')->allowedIPs = ['*'];
		}
		// Очистка всего кеша.
		if (
			Yii::$app->request->get('clear_cache')
			&& Yii::$app->user->can('clear_cache')
		) {
			Yii::$app->cache->flush();
			Yii::$app->response->redirect(\common\helpers\UriHelper::getCleanUrl());
		}
	},
	'params' => $params,
];
