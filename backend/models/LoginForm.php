<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 23.06.2020
 * Time: 9:38
 */

namespace backend\models;


use common\helpers\CF;
use common\models\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
	public $isNewRecord;

	public $username;
	public $last_name;
	public $email;
	public $password;
	public $group;
	public $status;

	private $_user;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			['status', 'default', 'value' => User::STATUS_ACTIVE],
			['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_BLOCKED, User::STATUS_DELETED]],

			[['username', 'last_name', 'email', 'password'], 'filter', 'filter' => 'trim'],
			[['email'], 'unique', 'targetClass' => '\common\models\User'],
			[['username', 'last_name', 'email', 'password'], 'string', 'min' => 2, 'max' => 255],

			[['username', 'last_name', 'email', 'password', 'group'], 'required'],
			[['username', 'last_name'], 'validateName'],
			['email', 'validateEmail'],
		];
	}

	public function attributeLabels()
	{
		return [
			'username' => 'Имя',
			'last_name' => 'Фамилия',
			'email' => 'E-mail',
			'group' => 'Группа',
			'status' => 'Статус',
			'password' => 'Пароль',
		];
	}

	/**
	 * Validates the username.
	 * This method serves as the inline validation for username.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validateName($attribute, $params)
	{
		if (!preg_match('/^[а-яё\s-]+$/iu', $this->$attribute)) {
			$this->addError($attribute, 'Используйте русские буквы.');
		}
	}

	/**
	 * Проверка email.
	 * @param string $attribute
	 * @param array $params
	 */
	public function validateEmail($attribute, $params)
	{
		if (!preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $this->email)) {
			$this->addError($attribute, 'Некорректный e-mail.');
		}
	}

	/**
	 * Signs user up.
	 *
	 * @return User|null the saved model or null if saving fails
	 */
	public function signup()
	{
		if (!$this->validate()) {
			return null;
		}

		$user = new User();
		$user->username = $this->username;
		$user->last_name = $this->last_name;
		$user->email = $this->email;
		$user->status = $this->status;
		$user->setPassword($this->password);
		$user->generateAuthKey();

		return $user->save() ? $user : null;
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