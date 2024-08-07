<?php
$date = date('Y-m-d');

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'user' => [
            'class' => \common\components\UserComponent::class,
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-yii2', 'httpOnly' => true],
        ],
        'cache' => [
            'class' => \common\components\CacheComponent::class,
            'defaultDuration' => 60 * 60 * 1,
        ],
        'cacheFrontend' => [
            'class' => \common\components\CacheComponent::class,
            'defaultDuration' => 60 * 60 * 1,
            'cachePath' => Yii::getAlias('@frontend') . '/runtime/cache'
        ],
        'i18n' => [
            'translations' => [
                'yii2mod.rbac' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/rbac/messages',
                    'forceTranslation' => true,
                ],
            ],
        ],
        'formatter' => [
            'class' => '\yii\i18n\Formatter',
            'nullDisplay' => '&nbsp;',
            'thousandSeparator' => ' ',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy, HH:mm:ss',
            'timeFormat' => 'HH:mm:ss'
        ],
        'monolog' => [
            'class' => '\Mero\Monolog\MonologComponent',
            'channels' => [
                'main' => [
                    'handler' => [
                        [
                            'type' => 'stream',
                            'path' => "@log/monolog/{$date}/main.log",
                            'level' => 'debug'
                        ]
                    ],
                    'processor' => [],
                ],
                'auth' => [
                    'handler' => [
                        [
                            'type' => 'stream',
                            'path' => "@log/monolog/{$date}/auth.log",
                            'level' => 'debug'
                        ]
                    ],
                    'processor' => [],
                ],
            ],
        ],
        'authManager' => \common\components\AuthManagerComponent::class,
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'name' => 'Project name',
];
