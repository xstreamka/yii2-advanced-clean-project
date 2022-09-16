<?php
$date = date('Y-m-d');

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'cacheFrontend' => [
            'class' => 'yii\caching\FileCache',
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
                            'path' => "@frontend/runtime/logs/{$date}/main.log",
                            'level' => 'debug'
                        ]
                    ],
                    'processor' => [],
                ],
                'auth' => [
                    'handler' => [
                        [
                            'type' => 'stream',
                            'path' => "@frontend/runtime/logs/{$date}/auth.log",
                            'level' => 'debug'
                        ]
                    ],
                    'processor' => [],
                ],
            ],
        ],
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'name' => 'Yii2',
];
