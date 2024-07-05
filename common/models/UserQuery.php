<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev@agat.local
 * Date: 21.12.2022
 * Time: 15:05
 */

namespace common\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return UserQuery
     */
    public function active()
    {
        return $this->andWhere(['user.status' => User::STATUS_ACTIVE]);
    }

    /**
     * @param string|array $assignment
     * @return UserQuery
     */
    public function authAssignment($assignment)
    {
        return $this->joinWith(['authAssignment' => function (ActiveQuery $query) use ($assignment) {
            $query->where(['auth_assignment.item_name' => $assignment]);
        }], false);
    }
}
