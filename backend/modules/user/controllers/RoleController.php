<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\UserRoleForm;
use common\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class RoleController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['user_role_view'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['user_role_create'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['user_role_update'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['user_role_delete'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['get', 'post'],
                ],
            ],
        ];
    }

    /**
     * Группы.
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $roles = array_values(Yii::$app->authManager->getRoles());
        $dataProvider = new ArrayDataProvider([
            'allModels' => $roles,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Создать группу.
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new UserRoleForm();

        $auth = Yii::$app->authManager;
        $permissions = ArrayHelper::map($auth->getPermissions(), 'name', 'description');
        asort($permissions);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createGroup();
            $model->setPermissions();

            Yii::$app->session->setFlash('success', 'Группа успешно создана');

            return $this->redirect(Url::toRoute(['/user/role']));
        }

        return $this->render('create', [
            'model' => $model,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Изменить группу.
     * @param string $alias
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($alias)
    {
        $model = new UserRoleForm();

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($alias);
        $permissions = ArrayHelper::map($auth->getPermissions(), 'name', 'description');
        asort($permissions);

        if (!$role) throw new NotFoundHttpException('Запрошенная страница не найдена.');

        if ($model->load(Yii::$app->request->post())) {
            if ($role->name != $model->name && User::canCreateAuth($model->name)) {
                Yii::$app->session->setFlash('danger', 'Данный код уже используется');
            } else {
                $model->update($role);
                $model->setPermissions();

                Yii::$app->session->setFlash('success', 'Группа успешно изменена');

                return $this->refresh();
            }
        }

        $model->name = $role->name;
        $model->description = $role->description;
        $model->permissions = $model->getPermissions();

        return $this->render('update', [
            'model' => $model,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Удаление группы.
     * @param string $alias
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($alias)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($alias);

        if (!$role) throw new NotFoundHttpException('Запрошенная страница не найдена.');

        $auth->remove($role);

        Yii::$app->session->setFlash('success', 'Группа успешно удалена');

        return $this->redirect(['/user/role']);
    }

}
