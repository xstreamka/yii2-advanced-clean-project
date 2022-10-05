<?php

namespace common\models;

use common\helpers\CF;
use common\helpers\DevHelper;
use common\helpers\LogHelper;
use common\interfaces\UserInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username Логин
 * @property string $name Имя
 * @property string $surname Фамилия
 * @property string $email Email
 * @property integer $status Статус
 *
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string|null $verification_token Подтверждение почты
 * @property string|null $verification_email_at Дата подтверждения почты
 *
 * @property string|null $access_token Токен
 * @property string|null $access_token_expiration Срок жизни токена
 *
 * @property string $created_at Создан
 * @property string|null $updated_at Обновлен
 *
 * @property string $password write-only password
 *
 * @property array $group Группа пользователя
 * @property array $statusText Текст статуса юзера
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

    const VERIFICATIONS = [
        'email' => 'verification_email_at',
    ];

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
        return [
            [['username', 'name', 'surname', 'email', 'password_new'], 'filter', 'filter' => 'trim'],

            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => array_keys(self::STATUS_TEXT)],

            [['username', 'name', 'surname', 'email', 'password_new'], 'string', 'min' => 2, 'max' => 255],
            [['username'], 'match', 'pattern' => '/^\w+$/i', 'message' => 'Используйте латинские буквы.'],
            [['name', 'surname'], 'match', 'pattern' => '/^[а-яё\s-]+$/iu', 'message' => 'Используйте русские буквы.'],
            ['email', 'email'],
            [['username', 'email'], 'unique'],
            [['username', 'name', 'surname', 'email'], 'required'],

            ['group', 'each', 'rule' => ['string']],

            [['created_at', 'updated_at', 'verification_email_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'email' => 'Email',
            'verification_email_at' => 'Подтверждение почты',
            'password_new' => 'Новый пароль',
            'status' => 'Статус',
            'group' => 'Группа',
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
        $user = static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
        if ($user && (YII_ENV_LOCAL || time() < strtotime($user->access_token_expiration))) {
            return $user;
        }

        return null;
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
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * Найти пользователя по username или email.
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->where(['or',
                ['username' => $login],
                ['email' => $login],
            ])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->one();
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

    public function resetEmailVerificationToken()
    {
        $this->verification_token = null;
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates new access_token
     */
    public function generateAccessToken()
    {
        $transaction = Yii::$app->db->beginTransaction();

        $access_token = CF::selectForUpdate($this, 'access_token');
        $access_token_expiration = CF::selectForUpdate($this, 'access_token_expiration');

        // Генерирует новый токен если его нет, либо если вышло время.
        if (empty($access_token) || time() > strtotime($access_token_expiration)) {
            $access_token = $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
            $this->access_token_expiration = date('Y-m-d H:i:s', strtotime('+ 15 minutes'));
            $this->save();
            LogHelper::auth("{$this} generated new access_token: {$access_token}");
        }

        $transaction->commit();

        return $access_token;
    }

    /**
     * Removes access_token
     */
    public function removeAccessToken()
    {
        $this->access_token = null;
        $this->access_token_expiration = null;
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
    public function setGroup($roles = [])
    {
        // Должен быть массив.
        $roles = array_filter((array)$roles);

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

    /**
     * Проверенный пользователь с подтвержденными данными.
     * @return bool
     */
    public function isVerified()
    {
        return $this->verification(self::VERIFICATIONS['email']);
    }

    /**
     * Ссылка подтверждения почты.
     * @return string
     */
    public function getVerifyEmailLink(): string
    {
        return Url::to("/verify-email/{$this->verification_token}/", true);
    }

    /**
     * Подтверждение почты пользователя.
     * @return bool
     */
    public function emailVerification()
    {
        $this->generateEmailVerificationToken();
        $this->resetVerification(self::VERIFICATIONS['email']);
        $this->save(false);

        return Yii::$app->mailer->compose(
            ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
            ['user' => $this],
        )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($this->email)
            ->setSubject('Подтверждение почты аккаунта ' . Yii::$app->params['senderName'])
            ->send();
    }

    /**
     * Дата подтверждения.
     * Возвращает читабельную дату и время, либо null.
     * @return string|null
     */
    public function verification(string $field)
    {
        return $this->{$field} ? date('d.m.Y, H:i:s', strtotime($this->{$field})) : null;
    }

    /**
     * Установка даты подтверждения.
     * @param string $field
     * @return void
     */
    public function setVerification(string $field)
    {
        $this->{$field} = date('Y-m-d H:i:s');
    }

    /**
     * Сброс даты подтверждения.
     * @param string $field
     * @return void
     */
    public function resetVerification(string $field)
    {
        $this->{$field} = null;
    }
}
