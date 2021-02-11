<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 23.06.2020
 * Time: 16:03
 */

namespace common\helpers;


use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\BaseJson;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;

class CF
{
	public static function p($data)
	{
		if (Yii::$app->user->isAdmin()) {
			if (is_bool($data)) {
				$data = ($data ? 'true' : 'false');
			}
			echo '<pre>' . print_r($data, 1) . '</pre>';
		}
	}

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
	 * Вызов selectpicker при использовании websocket.
	 */
	public static function selectpicker()
	{
		echo Html::script('$(".selectpicker").selectpicker()');
	}

	/**
	 * Опции для selectpicker.
	 *
	 * @param bool|bool $multiple Множественный выбор
	 * @param null|string $prompt Ничего не выбрано
	 * @param bool $liveSearch Поиск
	 * @param bool $actionsBox Выбрать все
	 * @param int $count Макс. количество в строке
	 *
	 * @return string[]
	 */
	public static function getSelectpickerOptions($multiple = false, $prompt = false, $liveSearch = false, $actionsBox = false, $count = 3)
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
	 * Сортировка массива по нужному ключу.
	 *
	 * @param string $sort
	 * @param array $array
	 */
	public static function sortBy(string $sort, array &$array)
	{
		uasort($array, function ($a, $b) use ($sort) {
			return (int)($a[$sort] > $b[$sort]);
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
	 * Путь к логу.
	 * Создает папку для лога, если нужно.
	 * @param string $filename Файл
	 * @return string
	 */
	public static function getLog(string $filename = ''): string
	{
		$dir = Yii::getAlias('@log' . date('/Y-m-d/'));
		FileHelper::createDirectory($dir);
		return $dir . $filename;
	}
}