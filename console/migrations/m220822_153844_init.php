<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m220822_153844_init
 */
class m220822_153844_init extends Migration
{
    public $tableName = '{{%user}}';

    /** @var \yii\rbac\DbManager */
    private $authManager;
    private $roles;
    private $permissions;

    public function init()
    {
        $this->authManager = Yii::$app->authManager;

        // Роли.
        $this->roles = [
            'superadmin' => 'Суперадмин',
            'admin' => 'Админ',
            'moderator' => 'Модератор',
            'user' => 'Пользователь',
        ];

        // Разрешения.
        $this->permissions = [
            'dashboard' => 'Админ панель',
            'clear_cache' => 'Очистка кеша',

            'user_view' => 'Пользователи (просмотр)',
            'user_create' => 'Пользователи (создание)',
            'user_update' => 'Пользователи (изменение)',
            'user_delete' => 'Пользователи (удаление)',

            'user_role_view' => 'Группы (просмотр)',
            'user_role_create' => 'Группы (создание)',
            'user_role_update' => 'Группы (изменение)',
            'user_role_delete' => 'Группы (удаление)',

            'user_permission_view' => 'Разрешения (просмотр)',
            'user_permission_create' => 'Разрешения (создание)',
            'user_permission_update' => 'Разрешения (изменение)',
            'user_permission_delete' => 'Разрешения (удаление)',
        ];
    }

    public function safeUp()
    {
        // Фикс user_id таблицы auth_assignment.
        $this->execute('ALTER TABLE auth_assignment ALTER COLUMN user_id TYPE integer USING user_id::integer');

        // Добавляем роли.
        foreach ($this->roles as $roleName => $roleDescription) {
            $newRole = $this->authManager->createRole($roleName);
            $newRole->description = $roleDescription;
            $this->authManager->add($newRole);
        }

        // Добавляем разрешения.
        foreach ($this->permissions as $permissionName => $permissionDescription) {
            $newPermission = $this->authManager->createPermission($permissionName);
            $newPermission->description = $permissionDescription;
            $this->authManager->add($newPermission);
        }

        // Создаем табличку пользователей.
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

        // Добавляем права.
        $superadmin = $this->authManager->getRole('superadmin');
        $this->authManager->assign($superadmin, 1);
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE auth_assignment ALTER COLUMN user_id TYPE varchar(64) USING user_id::varchar');

        foreach ($this->permissions as $permissionName => $permissionDescription) {
            if ($permission = $this->authManager->getPermission($permissionName)) {
                $this->authManager->remove($permission);
            }
        }

        foreach ($this->roles as $roleName => $roleDescription) {
            if ($role = $this->authManager->getRole($roleName)) {
                $this->authManager->remove($role);
            }
        }

        $this->dropTable($this->tableName);
    }
}
