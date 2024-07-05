<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev@agat.local
 * Date: 17.05.2023
 * Time: 18:08
 */

namespace common\components;

use Yii;
use yii\caching\FileCache;
use yii\caching\FileDependency;

class CacheComponent extends FileCache
{
    /**
     * @param bool $deleteEmpty true, если не нужно кешировать пустые данные
     * @return false|mixed
     */
    public function getOrSet($key, $callable, $duration = null, $dependency = null, $deleteEmpty = false)
    {
        $value = parent::getOrSet($key, $callable, $duration, $dependency);

        if ($deleteEmpty && empty($value)) {
            parent::delete($key);
        }

        return $value;
    }

    /**
     * Получить зависимость.
     * @param mixed $key
     * @return FileDependency
     */
    public function getDependency($key): FileDependency
    {
        $file = Yii::getAlias("@backend/data/{$key}.txt");

        return new FileDependency(['fileName' => $file]);
    }

    /**
     * Обновить зависимость.
     * @param mixed $key
     * @return void
     */
    public function setDependency($key)
    {
        $file = Yii::getAlias("@backend/data/{$key}.txt");

        file_put_contents($file, date('Y-m-d H:i:s'));
    }
}