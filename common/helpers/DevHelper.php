<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.08.2022
 * Time: 15:55
 */

namespace common\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class DevHelper
{
    /**
     * Отправка бага на почту.
     * @param string $msg
     * @param mixed $addInfo
     * @param string|array|null $addEmail
     */
    public static function bugNotify($msg, $addInfo = '', $addEmail = null)
    {
        $time = date('d.m.Y H:i:s');

        if ($addInfo) {
            if ($addInfo instanceof \Exception) {
                $addInfo = self::getExceptionText($addInfo);
            } elseif (!is_string($addInfo)) {
                $addInfo = VarDumper::export($addInfo);
            }
            $addInfo = "<pre>{$addInfo}</pre>";
        }

        // Кому письмо.
        $to = Yii::$app->params['adminEmail'];
        // Еще кому.
        if ($addEmail) {
            // Приводим все к одному виду - массиву.
            if (!is_array($to)) {
                $to = [$to];
            }
            if (!is_array($addEmail)) {
                $addEmail = [$addEmail];
            }
            $to = array_merge($to, $addEmail);
        }

        $body = "{$time}<br><br>{$msg}<br><br>{$addInfo}";

        Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom(Yii::$app->params['errorEmail'])
            ->setSubject('Оповещение разработчиков при ошибках ' . Yii::$app->params['host'])
            ->setHtmlBody($body)
            ->send();
    }

    /**
     * Формирует строку с данными из исключения.
     * @param \Exception $exception
     * @return string
     */
    public static function getExceptionText($exception): string
    {
        // Проверка входящих данных. Проверяю тут, чтоб не было дополнительного крита...
        if (!$exception || !($exception instanceof \Exception)) {
            return '';
        }

        return "Файл: {$exception->getFile()} \nСтрока: {$exception->getLine()} \nСообщение: {$exception->getMessage()}";
    }

    /**
     * Вывод ошибки при создании модели.
     * @param bool $asHtml
     * @return string
     */
    public static function errorWhileCreating($asHtml = true): string
    {
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];

        /** @var ActiveRecord $model */
        $model = $debug['object'] ?? null;

        $errorMsg = VarDumper::export([
            'file' => $debug['file'],
            'line' => $debug['line'],
            'method' => $debug['class'] . $debug['type'] . $debug['function'] . '()',
            'args' => $debug['args'],
            'errors' => $model->errors ?? [],
            'attributes' => $model->attributes ?? [],
        ]);

        return
            'Ошибка создания "' . ($model instanceof ActiveRecord ? $model->tableName() : null) . '".'
            . ($asHtml ? "<br><br><pre>{$errorMsg}</pre>" : "\n\n$errorMsg");
    }
}