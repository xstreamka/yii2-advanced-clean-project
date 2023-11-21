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

        if ($user->isAdmin() || $user->can('change_user')) {

            if ($user->isAdmin()) {
                $role = [
                    [
                        'label' => 'Группы',
                        'url' => ['/user/role/index'],
                    ],
                    [
                        'label' => 'Разрешения',
                        'url' => ['/user/permission/index'],
                    ],
                ];
            }

            $menuItems[] = [
                'label' => 'Пользователи',
                'url' => ['/user'],
                'items' => array_merge([
                    [
                        'label' => 'Список пользователей',
                        'url' => ['/user'],
                    ],
                ], $role ?? [])
            ];
        }

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

        if (YII_ENV_LOCAL) {
            $menuItems[] = [
                'label' => 'Gii',
                'url' => '/gii/',
                'linkOptions' => ['target' => '_blank'],
            ];
        }

        if ($user->can('clear_cache')) {
            $menuItems[] = [
                'label' => 'Очистить кэш',
                'url' => CF::getCleanUrl() . '?clear_cache=1',
            ];
        }

        if (!$user->isGuest) {
            $menuItems[] = [
                'label' => 'Выйти (' . $user->identity->username . ')',
                'url' => ['/site/logout']
            ];
        }

        return $menuItems;
    }
}