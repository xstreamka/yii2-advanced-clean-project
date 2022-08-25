<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 14:20
 */

namespace backend\modules\user\controllers;

use backend\modules\user\models\UserRoleForm;
use common\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PermissionController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['superadmin']
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
     * Разрешения.
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $filter = Yii::$app->request->get('filter') ?? null;
        $permission = $filter ? array_values($auth->getPermissionsByRole($filter)) : array_values($auth->getPermissions());
        $dataProvider = new ArrayDataProvider([
            'allModels' => $permission,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filter' => $filter,
        ]);
    }

    /**
     * Создать разрешение.
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new UserRoleForm();

        if ($model->load(Yii::$app->request->post())) {
            if (User::canCreateAuth($model->name)) {
                $auth = Yii::$app->authManager;
                $new_permission = $auth->createPermission($model->name);
                $new_permission->description = $model->description;
                $auth->add($new_permission);

                Yii::$app->session->setFlash('success', 'Разрешение успешно создано');

                return $this->redirect(Url::toRoute(['/user/permission']));
            } else {
                Yii::$app->session->setFlash('danger', 'Данный код уже используется');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Изменить разрешение.
     * @param string $alias
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($alias)
    {
        $model = new UserRoleForm();

        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($alias);

        if (!$permission) throw new NotFoundHttpException('Запрошенная страница не найдена.');

        if ($model->load(Yii::$app->request->post())) {
            if ($permission->name != $model->name && !User::canCreateAuth($model->name)) {
                Yii::$app->session->setFlash('danger', 'Данный код уже используется');
            } else {
                $model->update($permission);

                Yii::$app->session->setFlash('success', 'Разрешение успешно изменено');

                return $this->refresh();
            }
        }

        $model->name = $permission->name;
        $model->description = $permission->description;

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удалить разрешение.
     * @param string $alias
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($alias)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($alias);

        if (!$permission) throw new NotFoundHttpException('Запрошенная страница не найдена.');

        $auth->remove($permission);

        Yii::$app->session->setFlash('success', 'Разрешение успешно удалено');

        return $this->redirect(['/user/permission']);
    }
}