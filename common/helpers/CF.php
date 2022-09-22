<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:15
 */

namespace common\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class CF
{
    public static function numberFormat($number, int $decimals = null)
    {
        $explode = explode('.', $number);
        if (!empty($explode[1]) && $explode[1] > 0) {
            if (!empty($decimals)) {
                return number_format($number, $decimals, '.', ' ');
            } else {
                return number_format($number, strlen($explode[1]), '.', ' ');
            }
        } else {
            if (!empty($decimals)) {
                return number_format($number, $decimals, '.', ' ');
            } else {
                return number_format($number, 0, '.', ' ');
            }
        }
    }

    /**
     * Проверка на JSON.
     * @param $string
     * @return bool
     */
    public static function isJson($string): bool
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Вызов selectpicker при использовании websocket.
     */
    public static function selectpicker()
    {
        echo Html::script('$(".selectpicker").selectpicker()');
    }

    /**
     * Опции для selectpicker.
     * @param null|false|string $prompt Ничего не выбрано
     * @param bool $multiple Множественный выбор
     * @param bool $liveSearch Поиск
     * @param bool $actionsBox Выбрать все
     * @param int $count Максимальное количество в строке
     * @return string[]
     */
    public static function getSelectpickerOptions($prompt = false, $multiple = false, $liveSearch = false, $actionsBox = false, $count = 3)
    {
        $options = [
            'class' => 'selectpicker form-control',
        ];

        if (is_null($prompt)) {
            $options['prompt'] = 'Не выбрано';
        } elseif (is_string($prompt)) {
            $options['prompt'] = $prompt;
        }
        if ($liveSearch) {
            $options['data-live-search'] = 'true';
        }
        if ($actionsBox) {
            $options['data-actions-box'] = 'true';
        }
        if ($multiple) {
            $options['multiple'] = 'true';
            $options['data-selected-text-format'] = 'count>' . $count;
        }

        return $options;
    }

    /**
     * @param integer $n
     * @param array $forms [0 => значений, 1 => значение, 2 => значения]
     * @param bool $withN
     *
     * @return mixed
     */
    public static function plural($n, $forms, $withN = false)
    {
        $result = $n % 10 == 1 && $n % 100 != 11 ? $forms[1] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[2] : $forms[0]);
        if ($withN) {
            $result = $n . ' ' . $result;
        }
        return $result;
    }

    /**
     * Возвращает название класса без пространства имен.
     * В моделях использовать $model->formName()
     * @param string|object $class namespace или class
     * @return string
     */
    public static function getClassName($class): string
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return explode('\\', $class)[2];
    }

    /**
     * Возвращает значение поля модели во время POST запроса.
     * p.s. требуется в некоторых случаях.
     * @param string $className Имя класса
     * @param string $field Искомое поле
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    public static function getModelFieldFromPost(string $className, string $field, $default)
    {
        $result = $default;

        $post = Yii::$app->request->post();

        if (isset($post[$className][$field]) && $post[$className][$field] !== '') {
            $result = $post[$className][$field];
        }

        return $result;
    }

    /**
     * Сортировка массива по нужному ключу.
     * Еще есть ArrayHelper::multisort();
     *
     * @param string $sort Ключ
     * @param array $array Массив
     * @param int $orderBy Направление сортировки, по умолчанию А-Я.
     */
    public static function sortBy(string $sort, array &$array, $orderBy = SORT_ASC)
    {
        uasort($array, function ($a, $b) use ($sort, $orderBy) {
            if ($orderBy === SORT_ASC) {
                if ($a[$sort] > $b[$sort]) return 1;
            }
            if ($orderBy === SORT_DESC) {
                if ($a[$sort] < $b[$sort]) return 1;
            }
        });
    }

    /**
     * Сортировка по алфавиту + языку.
     * @param array $array Исходный массив
     * @param false $byKey Сортировка по ключу. По умолчанию по значению.
     */
    public static function sortByLocale(array &$array, $byKey = false)
    {
        $func = $byKey ? 'uksort' : 'uasort';
        $func($array, function ($a, $b) {
            if (ord($a) > 122 && ord($b) > 122) {
                return (int)($a > $b);
            }
            if (ord($a) > 122 || ord($b) > 122) {
                return (int)($a < $b);
            }
            return 0;
        });
    }

    /**
     * Получить номер вида +7(999) 999-99-99
     *
     * @param string $phone
     *
     * @return string|string[]|null
     */
    public static function getPhoneWithoutSpace(string $phone)
    {
        $number = preg_replace('/\D+/', '', $phone);
        if (strlen($number) === 10) {
            $result = preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', "+7($1) $2-$3-$4", $number);
        } elseif (strlen($number) === 11) {
            $result = preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', "+7($2) $3-$4-$5", $number);
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Получить номер вида +7 (999) 999-99-99
     *
     * @param string $phone
     *
     * @return string|string[]|null
     */
    public static function getPhoneWithSpace(string $phone)
    {
        $number = preg_replace('/\D+/', '', $phone);
        if (strlen($number) === 10) {
            $result = preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', "+7 ($1) $2-$3-$4", $number);
        } elseif (strlen($number) === 11) {
            $result = preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', "+7 ($2) $3-$4-$5", $number);
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Доп поля для проверки на спам.
     * @return string
     */
    public static function antispamFields(): string
    {
        return Html::input('text', 'name', '', ['class' => 'no-visible'])
            . Html::input('text', 'phone', '', ['class' => 'no-visible']);
    }

    /**
     * Обрезает строку до нужного размера.
     * @param string $str Строка
     * @param int $col Число символов
     * @return string
     */
    public static function short(string $str, int $col = 100): string
    {
        if (iconv_strlen($str, 'utf-8') > $col) {
            $str = rtrim(mb_strimwidth(strip_tags($str), 0, $col), '!,.- ') . '...';
        }
        return $str;
    }

    /**
     * Создает массив с ключами из значений.
     * @param array $array
     * @return array
     */
    public static function getArrayWithKeysFromValues(array $array): array
    {
        $result = [];

        foreach ($array as $item) {
            $result[$item] = $item;
        }

        return $result;
    }

    /**
     * Преобразует значения многомерного массива в один массив.
     * @param array $array Многомерный массив
     * @return array Обычный массив
     */
    public static function getArrayFromArray(array $array) : array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
        return array_values(iterator_to_array($iterator, false));
    }

    /**
     * Телефон формата 9876543210 без +7 или 8.
     * @param string $phone
     * @return string|null
     */
    public static function getClearPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $phone);
        if (in_array($phone[0], [7, 8])) {
            $phone = substr($phone, 1);
        }

        return iconv_strlen($phone) === 10 ? $phone : null;
    }

    /**
     * Получить чистый URL.
     * @return false|mixed|string
     */
    public static function getCleanUrl()
    {
        return strstr(Yii::$app->request->url, '?', true) ?: Yii::$app->request->url;
    }

    /**
     * Ищет совпадения адреса.
     * @param string|array $urls
     * @return bool
     */
    public static function checkUrl($urls): bool
    {
        foreach ((array)$urls as $url) {
            if (strpos(self::getCleanUrl(), $url) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Является ли адрес API.
     * @return bool
     */
    public static function isApiUrl(): bool
    {
        return self::checkUrl('/api/');
    }

    /**
     * Общий SELECT FOR UPDATE.
     * Пример:
     *      $transaction = Yii::$app->db->beginTransaction();
     *      $access_token = CF::selectForUpdate($this, 'access_token');
     *      $access_token_expiration = CF::selectForUpdate($this, 'access_token_expiration');
     *      $transaction->commit();
     * @param ActiveRecord $model
     * @param string $field
     * @return string|null
     */
    public static function selectForUpdate(ActiveRecord $model, string $field)
    {
        $sql = $model::find()
            ->where(['id' => $model->id])
            ->select($field)
            ->createCommand()
            ->getRawSql();

        $query = $model::findBySql("{$sql} FOR UPDATE")->asArray()->one();

        return $query[$field] ?? null;
    }
}