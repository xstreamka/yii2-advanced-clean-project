<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 23.06.2020
 * Time: 10:10
 */

namespace backend\models;


use Yii;
use yii\base\Model;
use yii\rbac\Role;

class UserRoleForm extends Model
{
	public $name;
	public $description;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'unique', 'targetClass' => '\backend\models\AuthItem'],

			[['name', 'description'], 'filter', 'filter' => 'trim'],
			[['name', 'description'], 'string', 'min' => 3, 'max' => 50],
			[['name'], 'match', 'pattern' => '/^[a-z_]*$/i'],
			[['name', 'description'], 'required'],
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
	 * Изменяет группу.
	 * @param Role $role
	 */
	public function updateGroup(Role $role)
	{
		$roleOldName = $role->name;
		$role->name = $this->name;
		$role->description = $this->description;
		Yii::$app->authManager->update($roleOldName, $role);
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
	 * @param $permissions
	 * @throws \yii\base\Exception
	 */
	public function setPermissions($permissions)
	{
		if (empty($permissions)) {
			$permissions = [];
		}
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($this->name);

		$auth->removeChildren($role);
		foreach ($permissions as $permission) {
			$auth->addChild($role, $auth->getPermission($permission));
		}
	}
}