<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 12:19
 */

namespace backend\widgets;

use common\helpers\CF;
use Yii;
use yii\bootstrap\Widget;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

class Menu extends Widget
{
    /**
     * Админка.
     */
    public function run()
    {
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);

        echo Html::tag('div', '', ['class' => 'me-auto']);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav mb-2 mb-md-0'],
            'items' => $this->getItems(),
        ]);

        NavBar::end();

        // Стили для submenu.
        $css = <<<CSS
    .dropdown .dropdown-submenu {
      display: none;
      position: absolute;
      left: 100%;
      top: -7px;
    }
    
    .dropdown:hover>.dropdown-submenu {
      display: block;
    }
CSS;

        Yii::$app->view->registerCss($css);
    }

    /**
     * Элементы меню.
     * @return array
     */
    public function getItems(): array
    {
        $user = Yii::$app->user;

        $menuItems = [
            [
                'label' => 'Главная',
                'url' => Yii::$app->homeUrl
            ],
            [
                'label' => 'Сайт',
                'url' => '/',
                'linkOptions' => ['target' => '_blank'],
            ],
        ];

        /**
         * User.
         */
        $userItems = [];

        if ($user->can('system_user_view')) {
            $userItems[] = [
                'label' => 'Список пользователей',
                'url' => ['/user/user/index'],
            ];
        }

        if ($user->can('system_user_role_view')) {
            $userItems[] = [
                'label' => 'Группы',
                'url' => ['/user/role/index'],
            ];
        }

        if ($user->can('system_user_permission_view')) {
            $userItems[] = [
                'label' => 'Разрешения',
                'url' => ['/user/permission/index'],
            ];
        }

        // User.
        if (!empty($userItems)) {
            $menuItems[] = [
                'label' => 'Пользователи',
                'url' => ['/user'],
                'items' => $userItems,
            ];
        }

        /**
         * Rbac.
         */
        if ($user->isSuperadmin()) {
            $menuItems[] = [
                'label' => 'Rbac',
                'url' => ['/rbac'],
                'items' => [
                    [
                        'label' => 'Назначения',
                        'url' => ['/rbac/assignment/index'],
                    ],
                    [
                        'label' => 'Роли',
                        'url' => ['/rbac/role/index'],
                    ],
                    [
                        'label' => 'Разрешения',
                        'url' => ['/rbac/permission/index'],
                    ],
                    [
                        'label' => 'Правила',
                        'url' => ['/rbac/rule/index'],
                    ],
                    [
                        'label' => 'Маршруты',
                        'url' => ['/rbac/route/index'],
                    ],
                ]
            ];
        }

        /**
         * New.
         */
        $agDevItems = [];

        if ($user->can('new')) {
            $agDevItems[] = [
                'label' => 'New item',
                'url' => ['/'],
            ];
        }

        /**
         * Продолжение меню.
         */

        // New.
        if (!empty($agDevItems)) {
            $menuItems[] = [
                'label' => 'New',
                'url' => ['/'],
                'items' => $agDevItems,
            ];
        }

        /**
         * Gii.
         */
        if (YII_ENV_LOCAL) {
            $menuItems[] = [
                'label' => 'Gii',
                'url' => ['../gii'],
                'linkOptions' => ['target' => '_blank'],
            ];
        }

        /**
         * Кеш.
         */
        if ($user->can('system_clear_cache')) {
            $menuItems[] = [
                'label' => 'Очистить кэш',
                'url' => CF::getCleanUrl() . '?clear_cache=1',
            ];
        }

        /**
         * Выход.
         */
        if (!$user->isGuest) {
            $menuItems[] = [
                'label' => 'Выйти (' . $user->identity->username . ')',
                'url' => ['/site/logout']
            ];
        }

        return $menuItems;
    }
}