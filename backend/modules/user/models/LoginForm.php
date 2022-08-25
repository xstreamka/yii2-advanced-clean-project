<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:14
 */

namespace backend\modules\user\models;

use common\models\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $isNewRecord;

    public $username;
    public $lastname;
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

            [['username', 'lastname', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['email'], 'unique', 'targetClass' => '\common\models\User'],
            [['username', 'lastname', 'email', 'password'], 'string', 'min' => 2, 'max' => 255],

            [['username', 'lastname', 'email', 'password', 'group'], 'required'],
            [['username', 'lastname'], 'match', 'pattern' => '/^[а-яё\s-]+$/iu', 'message' => 'Используйте русские буквы.'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'lastname' => 'Фамилия',
            'email' => 'Email',
            'group' => 'Группа',
            'status' => 'Статус',
            'password' => 'Пароль',
        ];
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
        $user->lastname = $this->lastname;
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