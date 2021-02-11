<?php


namespace common\helpers;


use Yii;
use yii\helpers\Html;

class DevHelper
{
	public static function nicePrint($variable) : string
	{
		if (is_bool($variable)) {
			$variable = 'bool: ' . ($variable ? 'true' : 'false');
		}
		$html = Html::tag('pre', print_r($variable, true), [
			'style' => ['border' => '2px solid red']
		]);
		return $html;
	}

	/**
	 * Отправка бага на почту.
	 * @param string $msg
	 */
	public static function bugNotify(string $msg)
	{
		Yii::$app->mailer->compose()
			->setTo(Yii::$app->params['adminEmail'])
			->setFrom(Yii::$app->params['fromEmail'])
			->setSubject('Оповещение разработчиков при ошибках сайта ' . Yii::$app->params['host'])
			->setHtmlBody($msg)
			->send();
	}
}