<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 22.12.2020
 * Time: 12:58
 */

namespace common\components;


use Yii;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;
use yii\web\User;

/**
 * Class UserComponent
 * @package common\components
 */
class UserComponent extends User
{
	public function init()
	{
		$this->enableAutoLogin = true;
		$this->getIdentityAndDurationFromCookie();
		parent::init();
	}

	/**
	 * Очистка сессии при блокировке пользователя.
	 * @return array|null
	 */
	protected function getIdentityAndDurationFromCookie()
	{
		$value = Yii::$app->getRequest()->getCookies()->getValue($this->identityCookie['name']);
		if ($value === null) {
			return null;
		}
		$data = json_decode($value, true);
		if (is_array($data) && count($data) == 3) {
			list($id, $authKey, $duration) = $data;
			/* @var $class IdentityInterface */
			$class = $this->identityClass;
			$identity = $class::findIdentity($id);
			if ($identity !== null) {
				if (!$identity instanceof IdentityInterface) {
					throw new InvalidValueException('$class::findIdentity() must return an object implementing IdentityInterface.');
				} elseif (!$identity->validateAuthKey($authKey)) {
					if(Yii::$app->session->has($this->idParam)) {
						Yii::$app->session->remove($this->idParam);
						$this->removeIdentityCookie();
					}
				} else {
					return ['identity' => $identity, 'duration' => $duration];
				}
			}
		}
		$this->removeIdentityCookie();
		return null;
	}

	/**
	 * Проверка разрешений.
	 * @param string|array $permission Разрешения
	 * @return bool
	 */
	public function can($permissions, $params = [], $allowCaching = true): bool
	{
		// Боженька все видит.
		if ($this->isSuperadmin()) {
			return true;
		}

		$permissions = (array)$permissions;

		foreach ($permissions as $permission) {
			if (parent::can($permission, $params, $allowCaching)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isSuperadmin()
	{
		return !$this->isGuest && $this->identity->hasGroup(['superadmin']);
	}

	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return !$this->isGuest && $this->identity->hasGroup(['superadmin', 'admin']);
	}

	/**
	 * @return bool
	 */
	public function isModer()
	{
		return !$this->isGuest && $this->identity->hasGroup(['moderator']);
	}

	/**
	 * @return bool
	 */
	public function isUser()
	{
		return !$this->isGuest && $this->identity->hasGroup(['user']);
	}
}