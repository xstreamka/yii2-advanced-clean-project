<?php
namespace common\models;

use common\helpers\CF;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $last_name
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property IdentityInterface|null $identity
 * @property array $group
 * @property bool $validated Включить доп функции валидации или нет
 * @property $statusText Текст статуса
 */
class User extends ActiveRecord implements IdentityInterface
{
	public $validated = true;

    const STATUS_DELETED = 0;
    const STATUS_PROCESS = 7;
    const STATUS_BLOCKED = 8;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

	const STATUS_TEXT = [
		self::STATUS_DELETED => 'Удален',
		self::STATUS_PROCESS => 'В процессе',
		self::STATUS_BLOCKED => 'Заблокирован',
		self::STATUS_INACTIVE => 'Неактивен',
		self::STATUS_ACTIVE => 'Активен'
	];

	const ACTIVE_YES = 1;
	const ACTIVE_NO = 0;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
	    $rules = [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
		    ['status', 'in',
			    'range' => [
				    self::STATUS_ACTIVE,
				    self::STATUS_INACTIVE,
				    self::STATUS_BLOCKED,
				    self::STATUS_PROCESS,
				    self::STATUS_DELETED
			    ]
		    ],

	        [['username', 'last_name', 'email'], 'filter', 'filter' => 'trim'],
	        [['email'], 'unique', 'targetClass' => '\common\models\User'],
	        [['username', 'last_name', 'email'], 'string', 'min' => 2, 'max' => 255],

	        ['email', 'required'],
        ];

	    if ($this->validated) {
		    $rules['username'] = ['username', 'required'];
		    $rules['last_name'] = ['last_name', 'required'];

	    	$rules['username_'] = ['username', 'validateName'];
		    $rules['last_name_'] = ['last_name', 'validateName'];

		    $rules['email_'] = ['email', 'validateEmail'];
	    }

	    if (!$this->isNewRecord) {
		    $rules['group'] = ['group', 'required'];
	    }

	    return $rules;
    }

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'username' => 'Имя',
			'last_name' => 'Фамилия',
			'email' => 'Email',
			'group' => 'Группа',
			'status' => 'Статус',
			'created_at' => 'Создан',
			'updated_at' => 'Обновлен',
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

	public function beforeSave($insert)
	{
		$this->updated_at = date('Y-m-d H:i:s');
		return parent::beforeSave($insert);
	}

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

	/**
	 * Найти пользователя по email.
	 * @param string $email
	 * @return User|null
	 */
	public static function findByEmail($email)
	{
		return static::findOne(['email' => $email, 'status' => User::STATUS_ACTIVE]);
	}

	/**
	 * Найти пользователя по email без учета активного статуса.
	 * @param string $email
	 * @return User|null
	 */
	public static function findByEmailWithAllStatus($email)
	{
		return static::findOne(['email' => $email]);
	}

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 *
	 * @throws Exception
	 */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

	/**
	 * Generates "remember me" authentication key
	 *
	 * @throws Exception
	 */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

	/**
	 * Generates new password reset token
	 *
	 * @throws Exception
	 */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

	/**
	 * Generates new token for email verification
	 *
	 * @throws Exception
	 */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

	/**
	 * Проверяем, можем ли создать группу или разрешение с таким кодом.
	 * @param string $name
	 * @return bool
	 */
	public static function canCreateAuth($name)
	{
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($name);
		$permission = $auth->getPermission($name);

		return !$role && !$permission;
	}

	/**
	 * Список групп.
	 * @param bool $onlyName Получить только name.
	 * @return array
	 */
	public static function getGroups($onlyName = false)
	{
		$roles = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description');

		if (!Yii::$app->user->identity->hasGroup('superadmin')) {
			unset($roles['superadmin']);
		}

		return $onlyName ? array_keys($roles) : $roles;
	}

	/**
	 * Получаем группу юзера.
	 * @return array
	 */
	public function getGroup()
	{
		return ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'description', 'name');
	}

	/**
	 * Назначаем группу юзеру.
	 * @param array $roles
	 * @throws \Exception
	 */
	public function setGroup(array $roles)
	{
		if (empty($roles)) {
			$roles = [];
		}
		$auth = Yii::$app->authManager;
		$auth->revokeAll($this->id);
		foreach ($roles as $role) {
			$userRole = $auth->getRole($role);
			$auth->assign($userRole, $this->id);
		}
	}

	/**
	 * Состоит ли в группе.
	 * @param string|array $groups
	 * @return bool
	 */
	public function hasGroup($groups)
	{
		$result = false;

		if (is_array($groups)) {
			foreach ($groups as $group) {
				if (in_array($group, $this->group)) {
					return true;
				}
			}
		} else {
			$result = in_array($groups, $this->group);
		}

		return $result;
	}

	/**
	 * Проверка разрешений.
	 * @param string $permission
	 * @return bool
	 */
	public function can(string $permission)
	{
		return Yii::$app->user->can($permission);
	}

	/**
	 * Получаем текст статуса юзера.
	 * @return mixed
	 */
	public function getStatusText()
	{
		return self::STATUS_TEXT[$this->status];
	}

	/**
	 * @param int $id
	 * @param bool $obj Объектом или массивом.
	 * @return array|bool|User|null
	 */
	public static function getUserById($id, $obj = false)
	{
		if (empty($id)) {
			return false;
		}
		$query = self::find()
			->where([
				'status' => self::STATUS_ACTIVE,
				'id' => $id
			]);
		if (!$obj) {
			$query->asArray();
		}
		return $query->one();
	}

	/**
	 * Получить пользователей по группе.
	 * @param string|array $group
	 * @param bool $obj Объектом или массивом.
	 * @return array|User[]
	 */
	public static function getUsersByGroup($group, $obj = false)
	{
		$query = self::find()
			->where(['status' => User::STATUS_ACTIVE])
			->innerJoin('auth_assignment', '{{auth_assignment.user_id}} = {{user.id}}')
			->andWhere(['auth_assignment.item_name' => $group]);
		if (!$obj) {
			$query->asArray();
		}
		return $query->all();
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}

	/**
	 * @return bool
	 */
	public function isInActive()
	{
		return $this->status == self::STATUS_INACTIVE;
	}

	/**
	 * @return bool
	 */
	public function isBlocked()
	{
		return $this->status === self::STATUS_BLOCKED;
	}

	/**
	 * @return bool
	 */
	public function isProcess()
	{
		return $this->status === self::STATUS_PROCESS;
	}

	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->status === self::STATUS_DELETED;
	}

	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->hasGroup(['superadmin', 'admin']);
	}

	/**
	 * @return bool
	 */
	public function isModer()
	{
		return $this->hasGroup(['moderator']);
	}

	/**
	 * @return bool
	 */
	public function isUser()
	{
		return $this->hasGroup(['user']);
	}

}
