<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m220822_153844_init
 */
class m220822_153844_init extends Migration
{
    public $tableName = '{{%user}}';

    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->notNull()->defaultValue("nextval('{$this->tableName}_id_seq'::regclass)")->comment('ID'),
            'username' => $this->string()->notNull()->unique()->comment('Логин'),
            'name' => $this->string()->notNull()->comment('Имя'),
            'surname' => $this->string()->notNull()->comment('Фамилия'),
            'email' => $this->string()->notNull()->unique()->comment('Email'),
            'status' => $this->smallInteger()->notNull()->defaultValue(User::STATUS_INACTIVE)->comment('Статус'),

            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string(32)->null()->comment('Подтверждение почты'),
            'verification_email_at' => $this->timestamp()->null()->comment('Дата подтверждения почты'),

            'access_token' => $this->string()->null()->comment('Токен'),
            'access_token_expiration' => $this->timestamp()->null()->comment('Срок жизни токена'),

            'created_at' => $this->timestamp()->notNull()->comment('Создан'),
            'updated_at' => $this->timestamp()->null()->comment('Обновлен'),
        ]);

        $this->addCommentOnTable($this->tableName, 'Пользователи');

        // Наполняем дефолтные данные.
        $this->insert($this->tableName, [
            'username' => 'admin',
            'name' => 'Админ',
            'surname' => 'Админов',
            'email' => 'admin@yiiframework.com',
            'verification_email_at' => date('Y-m-d H:i:s'),
            'status' => User::STATUS_ACTIVE,
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
