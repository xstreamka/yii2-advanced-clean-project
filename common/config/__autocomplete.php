<?php

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii {
    /**
     * @var \yii\web\Application|\yii\console\Application|__Application
     */
    public static $app;
}

/**
 * @property yii\rbac\DbManager $authManager 
 * @property \common\components\UserComponent $user The user component. This property is read-only.
 * @property \Mero\Monolog\MonologComponent $monolog The monolog component. Is a component for the Monolog library.
 * @property \common\components\CacheComponent|\yii\caching\CacheInterface|null $cache Cache.
 * @property \yii\caching\CacheInterface|null $cacheFrontend Frontend cache.
 *
 */
class __Application {
}
