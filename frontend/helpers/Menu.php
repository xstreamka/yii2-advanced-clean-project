<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 13.07.2020
 * Time: 9:04
 */

namespace frontend\helpers;


use Yii;
use yii\base\Widget;

class Menu extends Widget
{
	public function run()
	{
		$guestItems = [
			[
				'label' => 'Главная',
				'url' => '/'
			],
		];

		$userItems = [
			[
				'label' => 'Главная',
				'url' => '/'
			],
		];

		$moderItems = [
			[
				'label' => 'Главная',
				'url' => '/'
			],
		];

		$user = Yii::$app->user;

		if ($user->isGuest) {
			$menuItems = $guestItems;
		} else {
			if ($user->can('user')) {
				$menuItems = $userItems;
			} else {
				$menuItems = $moderItems;
			}
		}

		echo $this->render('/layouts/_top-menu', compact('menuItems'));
	}
}