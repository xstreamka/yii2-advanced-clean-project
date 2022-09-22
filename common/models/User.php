<?php

namespace common\models;

use common\helpers\DevHelper;
use common\interfaces\UserInterface;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username Имя
 * @property string $lastname Фамилия
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email Email
 * @property string $auth_key
 * @property integer $status Статус
 * @property integer $created_at Создан
 * @property integer $updated_at Обновлен
 *
 * @property string $password write-only password
 *
 * @property array $group Группы пользователя.
 * @property array $statusText Текст статуса юзера.
 */
class User extends ActiveRecord implements IdentityInterface, UserInterface
{
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

    /** @var bool Проверка всех полей */
    public $validateAll = true;
    public $password_new = '';


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
    public function rules()
    {
        $rules = [
            [['username', 'lastname', 'email', 'password_new'], 'filter', 'filter' => 'trim'],

            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => array_keys(self::STATUS_TEXT)],

            [['username', 'lastname', 'email'], 'string', 'min' => 2, 'max' => 255],
            [['email'], 'unique', 'targetClass' => '\common\models\User'],

            ['email', 'required'],
        ];

        if ($this->validateAll) {
            $rules[] = [['username', 'lastname'], 'required'];
            $rules[] = [['username', 'lastname'], 'match', 'pattern' => '/^[а-яё\s-]+$/iu', 'message' => 'Используйте русские буквы.'];
            $rules[] = ['email', 'email'];
        }

        if (!$this->isNewRecord) {
            $rules[] = ['group', 'required'];
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
            'lastname' => 'Фамилия',
            'email' => 'Email',
            'group' => 'Группа',
            'status' => 'Статус',
            'password_new' => 'Новый пароль',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        if (!$save = parent::save($runValidation, $attributeNames)) {
            $message = DevHelper::errorWhileCreating();
            DevHelper::bugNotify($message);
        }

        return $save;
    }

    public function __toString()
    {
        return "#{$this->id} {$this->username} {$this->email}";
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
            'status' => self::STATUS_INACTIVE
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
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
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

        return !$auth->getRole($name) && !$auth->getPermission($name);
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
     */
    public function setGroup(array $roles = [])
    {
        $auth = Yii::$app->authManager;

        // Удаляем старые группы.
        foreach ($this->group as $role) {
            $oldRole = $auth->getRole($role);
            $auth->revoke($oldRole, $this->id);
        }

        // Назначаем новые.
        foreach ($roles as $role) {
            $newRole = $auth->getRole($role);
            $auth->assign($newRole, $this->id);
        }
    }

    /**
     * Добавляем группу юзеру.
     * @param string|array $roles
     */
    public function addGroup($roles)
    {
        $roles = array_unique(array_merge($this->group, (array)$roles));

        $this->setGroup($roles);
    }

    /**
     * Состоит ли в группе.
     * @param string|array $groups
     * @return bool
     */
    public function hasGroup($groups): bool
    {
        return !empty(array_intersect((array)$groups, $this->group));
    }

    /**
     * Проверка разрешений.
     * @param string|array $permissions Разрешения
     * @return bool
     */
    public function can($permissions): bool
    {
        // Боженька все видит.
        if ($this->isSuperadmin()) {
            return true;
        }

        $auth = Yii::$app->authManager;

        foreach ((array)$permissions as $permission) {
            if ($auth->checkAccess($this->id, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSuperadmin(): bool
    {
        return $this->hasGroup('superadmin');
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasGroup(['superadmin', 'admin']);
    }

    /**
     * @return bool
     */
    public function isModer(): bool
    {
        return $this->hasGroup('moderator');
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->hasGroup('user');
    }

    /**
     * Получаем текст статуса юзера.
     * @return string
     */
    public function getStatusText()
    {
        return self::STATUS_TEXT[$this->status] ?? '';
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isInActive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * @return bool
     */
    public function isProcess(): bool
    {
        return $this->status === self::STATUS_PROCESS;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->status === self::STATUS_DELETED;
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
}
