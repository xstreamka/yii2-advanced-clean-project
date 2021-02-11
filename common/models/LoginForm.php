<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 * @property string $email
 * @property string $password
 *
 * @property User $_user
 */
class LoginForm extends Model
{
	public $email;
	public $password;

	private $_user;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			// username and password are both required
			[['email', 'password'], 'required'],
			// password is validated by validatePassword()
			['password', 'validatePassword'],
		];
	}

	public function attributeLabels()
	{
		return [
			'email' => 'E-mail',
			'password' => 'Пароль',
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validatePassword($attribute, $params)
	{
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user || !$user->validatePassword($this->password)) {
				$this->addError($attribute, 'Некорректный email или пароль.');
			}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return bool whether the user is logged in successfully
	 */
	public function login()
	{
		if ($this->validate()) {
			return Yii::$app->user->login($this->getUser(), 3600 * 24 * 30);
		}

		return false;
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	protected function getUser()
	{
		if ($this->_user === null) {
			$this->_user = User::findOne([
				'email' => $this->email,
				'status' => User::STATUS_ACTIVE
			]);
		}

		return $this->_user;
	}
}
