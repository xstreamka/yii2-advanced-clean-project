<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 15.07.2020
 * Time: 17:53
 */

namespace common\helpers;


use dosamigos\datepicker\DatePicker;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class BaseHtml extends \yii\helpers\BaseHtml
{
	/**
	 * @param array|null|string $selection
	 * @param array $items
	 * @param array $tagOptions
	 * @return string
	 */
	public static function renderSelectOptions($selection, $items, &$tagOptions = [])
	{
		if (is_null($selection)) $selection = [];
		$lines = [];
		$encodeSpaces = ArrayHelper::remove($tagOptions, 'encodeSpaces', false);
		$encode = ArrayHelper::remove($tagOptions, 'encode', true);
		if (isset($tagOptions['prompt'])) {
			$promptOptions = ['value' => ''];
			if (is_string($tagOptions['prompt'])) {
				$promptText = $tagOptions['prompt'];
			} else {
				$promptText = $tagOptions['prompt']['text'];
				$promptOptions = array_merge($promptOptions, $tagOptions['prompt']['options']);
			}
			$promptText = $encode ? static::encode($promptText) : $promptText;
			if ($encodeSpaces) {
				$promptText = str_replace(' ', '&nbsp;', $promptText);
			}
			$lines[] = static::tag('option', $promptText, $promptOptions);
		}

		$options = isset($tagOptions['options']) ? $tagOptions['options'] : [];
		$groups = isset($tagOptions['groups']) ? $tagOptions['groups'] : [];
		unset($tagOptions['prompt'], $tagOptions['options'], $tagOptions['groups']);
		$options['encodeSpaces'] = ArrayHelper::getValue($options, 'encodeSpaces', $encodeSpaces);
		$options['encode'] = ArrayHelper::getValue($options, 'encode', $encode);

		foreach ($items as $key => $value) {
			if (is_array($value)) {
				$groupAttrs = isset($groups[$key]) ? $groups[$key] : [];
				if (!isset($groupAttrs['label'])) {
					$groupAttrs['label'] = $key;
				}
				$attrs = ['options' => $options, 'groups' => $groups, 'encodeSpaces' => $encodeSpaces, 'encode' => $encode];

				if (is_array($selection) && count($selection) !== count($selection, COUNT_RECURSIVE)) {
					if (!empty($selection[$key]))
						$content = static::renderSelectOptions($selection[$key], $value, $attrs);
					else
						$content = static::renderSelectOptions(null, $value, $attrs);
				} else {
					$content = static::renderSelectOptions($selection, $value, $attrs);
				}


				$lines[] = static::tag('optgroup', "\n" . $content . "\n", $groupAttrs);
			} else {
				$attrs = isset($options[$key]) ? $options[$key] : [];
				$attrs['value'] = (string)$key;
				if (!array_key_exists('selected', $attrs)) {
					$attrs['selected'] = $selection !== null &&
						(!ArrayHelper::isTraversable($selection) && !strcmp($key, $selection)
							|| ArrayHelper::isTraversable($selection) && ArrayHelper::isIn($key, $selection));
				}
				$text = $encode ? static::encode($value) : $value;
				if ($encodeSpaces) {
					$text = str_replace(' ', '&nbsp;', $text);
				}
				$lines[] = static::tag('option', $text, $attrs);
			}
		}

		return implode("\n", $lines);
	}

	public static function dateRangeFilter(ActiveRecord $searchModel, $dateFromField = 'date_from', $dateToField = 'date_to')
	{
		return Html::tag('div', DatePicker::widget([
				'model' => $searchModel,
				'attribute' => $dateFromField,
				'language' => 'ru',
				'clientOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd'
				]
			]) . '<br>' .
			DatePicker::widget([
				'model' => $searchModel,
				'attribute' => $dateToField,
				'language' => 'ru',
				'clientOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd'
				]
			]), ['style' => ['width' => '230px']]);
	}

	public static function p($content = '', $options = [])
	{
		return self::tag('p', $content, $options);
	}

	public static function div($content = '', $options = [])
	{
		return self::tag('div', $content, $options);
	}

	public static function br($content = '', $options = [])
	{
		return self::tag('br', $content, $options);
	}

	public static function h1($content = '', $options = [])
	{
		return self::tag('h1', $content, $options);
	}

	public static function h2($content = '', $options = [])
	{
		return self::tag('h2', $content, $options);
	}

	public static function h3($content = '', $options = [])
	{
		return self::tag('h3', $content, $options);
	}

	public static function h4($content = '', $options = [])
	{
		return self::tag('h4', $content, $options);
	}

	public static function table($content = '', $options = [])
	{
		return self::tag('table', $content, $options);
	}

	public static function tr($content = '', $options = [])
	{
		return self::tag('tr', $content, $options);
	}

	public static function td($content = '', $options = [])
	{
		return self::tag('td', $content, $options);
	}
}