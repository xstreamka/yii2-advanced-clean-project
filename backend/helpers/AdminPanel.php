<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 23.06.2020
 * Time: 10:29
 */

namespace backend\helpers;


use Yii;

class AdminPanel
{
	public static function main_menu_items()
	{
		/** @var \common\components\UserComponent $user */
		$user = Yii::$app->user;
		$menu_items = [
			[
				'label' => 'Главная',
				'url' => ['/site/index']
			],
			[
				'label' => 'Сайт',
				'url' => '/',
				'linkOptions' => ['target' => '_blank'],
			],
		];

		if ($user->isAdmin()) {
			$menu_items[] = [
				'label' => 'Пользователи',
				'url' => ['/user'],
				'items' => [
					[
						'label' => 'Список пользователей',
						'url' => ['user/index'],
						'options' => ['class' => 'dropdown-submenu'],
					],
					[
						'label' => 'Группы',
						'url' => ['user/roles'],
						'options' => ['class' => 'dropdown-submenu'],
					],
					[
						'label' => 'Разрешения',
						'url' => ['user/permission'],
						'options' => ['class' => 'dropdown-submenu'],
					],
				]
			];
		}

		if (YII_ENV_LOCAL) {
			$menu_items[] = [
				'label' => 'Конструктор gii',
				'url' => ['../gii'],
				'linkOptions' => ['target' => '_blank'],
			];
		}

		$menu_items[] = [
			'label' => 'Выйти (' . $user->identity->username . ')',
			'url' => ['site/logout']
		];

		return $menu_items;
	}
}