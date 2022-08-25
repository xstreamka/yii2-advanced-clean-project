<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:26
 */

namespace backend\modules\user\models;

use Yii;
use yii\base\Model;
use yii\rbac\Permission;
use yii\rbac\Role;

/**
 * Class UserRoleForm
 * @package backend\models
 *
 * @property string $name
 * @property string $description
 */
class UserRoleForm extends Model
{
    public $name;
    public $description;
    public $permissions;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'filter', 'filter' => 'trim'],

            [['name', 'description'], 'required'],
            ['name', 'unique', 'targetClass' => '\backend\modules\user\models\AuthItem'],
            [['name', 'description'], 'string', 'min' => 3, 'max' => 50],
            [['name'], 'match', 'pattern' => '/^[a-z_]*$/i'],
            ['permissions', 'each', 'rule' => ['string']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Код',
            'description' => 'Название',
            'permissions' => 'Разрешения',
        ];
    }

    /**
     * Создает группу.
     */
    public function createGroup()
    {
        $auth = Yii::$app->authManager;
        $newRole = $auth->createRole($this->name);
        $newRole->description = $this->description;
        $auth->add($newRole);
    }

    /**
     * Изменяет группу / разрешение.
     * @param Role|Permission $object
     */
    public function update($object)
    {
        $oldName = $object->name;

        $object->name = $this->name;
        $object->description = $this->description;

        Yii::$app->authManager->update($oldName, $object);
    }

    /**
     * Получаем разрешения.
     * @return array
     */
    public function getPermissions()
    {
        return array_keys(Yii::$app->authManager->getPermissionsByRole($this->name));
    }

    /**
     * Ставим разрешения.
     */
    public function setPermissions()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->getRole($this->name);
        $auth->removeChildren($role);

        $permissions = $this->permissions ?: [];
        foreach ($permissions as $permission) {
            $auth->addChild($role, $auth->getPermission($permission));
        }
    }
}