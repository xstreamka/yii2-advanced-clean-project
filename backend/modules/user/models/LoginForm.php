<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:14
 */

namespace backend\modules\user\models;

use common\models\User;
use yii\base\Model;

class LoginForm extends Model
{
    public $isNewRecord = true;

    public $username;
    public $name;
    public $surname;
    public $email;
    public $password;
    public $group;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'name', 'surname', 'email', 'password'], 'filter', 'filter' => 'trim'],

            ['status', 'default', 'value' => User::STATUS_INACTIVE],
            ['status', 'in', 'range' => array_keys(User::STATUS_TEXT)],

            [['username', 'name', 'surname', 'email', 'password'], 'string', 'min' => 2, 'max' => 255],
            [['username'], 'match', 'pattern' => '/^\w+$/i', 'message' => 'Используйте латинские буквы.'],
            [['name', 'surname'], 'match', 'pattern' => '/^[а-яё\s-]+$/iu', 'message' => 'Используйте русские буквы.'],
            ['email', 'email'],
            [['username', 'email'], 'unique', 'targetClass' => '\common\models\User'],
            [['username', 'name', 'surname', 'email', 'password'], 'required'],

            ['group', 'each', 'rule' => ['string']],

            [['created_at', 'updated_at', 'verification_email_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'email' => 'Email',
            'password' => 'Пароль',
            'status' => 'Статус',
            'group' => 'Группа',
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
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->email = $this->email;
        $user->status = $this->status;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}