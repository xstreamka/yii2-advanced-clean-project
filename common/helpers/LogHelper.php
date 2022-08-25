<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 25.08.2022
 * Time: 15:06
 */

namespace common\helpers;

use Yii;

class LogHelper
{
    const MESSAGE_AUTH_SUCCESS = 'Авторизация успешна';
    const MESSAGE_AUTH_ERROR = 'Авторизация не успешна';
    const MESSAGE_LOGOUT = 'Пользователь вышел';

    const TYPE_DEBUG = 'debug';
    const TYPE_INFO = 'info';
    const TYPE_NOTICE = 'notice';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_CRITICAL = 'critical';
    const TYPE_ALERT = 'alert';
    const TYPE_EMERGENCY = 'emergency';

    /**
     * Основной метод.
     * @param string $type Тип
     * @param mixed $message Сообщение, можно массив
     */
    public static function log(string $type, $message, $logName = 'main')
    {
        // Обработка массива.
        if (!is_string($message)) {
            $message = var_export($message, true);
        }
        // Цепляем идентификатор.
        $message .= self::identity();

        $logger = Yii::$app->monolog->getLogger($logName);
        $logger->log($type, $message);
    }


    public static function debug($message)
    {
        self::log(self::TYPE_DEBUG, $message);
    }

    public static function info($message)
    {
        self::log(self::TYPE_INFO, $message);
    }

    public static function notice($message)
    {
        self::log(self::TYPE_NOTICE, $message);
    }

    public static function warning($message)
    {
        self::log(self::TYPE_WARNING, $message);
    }

    public static function error($message)
    {
        self::log(self::TYPE_ERROR, $message);
    }

    public static function critical($message)
    {
        self::log(self::TYPE_CRITICAL, $message);
    }

    public static function alert($message)
    {
        self::log(self::TYPE_ALERT, $message);
    }

    public static function emergency($message)
    {
        self::log(self::TYPE_EMERGENCY, $message);
    }


    /**
     * Идентификация.
     * @return string
     */
    private static function identity(): string
    {
        if (Yii::$app instanceof \yii\console\Application) {
            $identity = 'cli';
        } elseif (Yii::$app->user->isGuest) {
            $identity = 'guest';
        } else {
            $identity = Yii::$app->user->identity;
        }

        return " ({$identity})";
    }

    /**
     * Общие логи.
     * @param mixed $message
     * @param string $type
     */
    public static function main($message, string $type = self::TYPE_INFO)
    {
        self::log($type, $message);
    }

    /**
     * Логи авторизации.
     * @param mixed $message
     * @param string $type
     */
    public static function auth($message, string $type = self::TYPE_INFO)
    {
        self::log($type, $message, 'auth');
    }
}