<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 22.08.2022
 * Time: 13:30
 */

namespace common\interfaces;

interface UserInterface
{
    /**
     * Проверка разрешений.
     * @param string|array $permissions Разрешения
     * @return bool
     */
    public function can($permissions): bool;

    /**
     * @return bool
     */
    public function isSuperadmin(): bool;

    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return bool
     */
    public function isModer(): bool;

    /**
     * @return bool
     */
    public function isUser(): bool;
}