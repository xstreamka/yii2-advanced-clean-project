<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 09.10.2020
 * Time: 17:25
 */

namespace common\helpers;


use Yii;

class UriHelper
{
	/**
	 * Получить чистый URL.
	 * @return false|mixed|string
	 */
	public static function getCleanUrl()
	{
		return strstr(Yii::$app->request->url, '?', true) ?
			strstr(Yii::$app->request->url, '?', true)
			: Yii::$app->request->url;
	}
}