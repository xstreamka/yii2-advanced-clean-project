<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev@agat.local
 * Date: 12.01.2024
 * Time: 10:07
 */

namespace common\components;

use yii\helpers\ArrayHelper;

class AuthManagerComponent extends \yii\rbac\DbManager
{
    /**
     * Список разрешений.
     * @return array
     */
    public function getPermissionsList(): array
    {
        $result = [];

        $permissions = ArrayHelper::map($this->getPermissions(), 'name', 'description');
        asort($permissions);

        foreach ($permissions as $permissionCode => $permissionName) {
            if (preg_match('/^system_user_.+$/', $permissionCode)) {
                $group = 'Пользователи';
            } elseif (preg_match('/^system_.+$/', $permissionCode)) {
                $group = 'Системные';
            } else {
                $group = 'Основные';
            }
            $result[$group][$permissionCode] = $permissionName;
        }

        return $result;
    }
}