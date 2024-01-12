<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\LoginForm;
use common\models\User;
use common\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `user` module
 */
class UserController extends Controller
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
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['system_user_view'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['system_user_create'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['system_user_update'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['system_user_delete'],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'groups' => User::getGroups()
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->isSuperadmin() && !Yii::$app->user->isSuperadmin()) {
            throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                $user->group = $model->group;

                Yii::$app->session->setFlash('success', 'Пользователь успешно создан');

                return $this->redirect(['user/index']);
            }
        }

        $model->status = User::STATUS_ACTIVE;

        return $this->render('create', [
            'model' => $model,
            'groups' => User::getGroups(),
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->isSuperadmin() && !Yii::$app->user->isSuperadmin()) {
            throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->password_new)) {
                $model->setPassword($model->password_new);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен');

                return $this->redirect(['view',	'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'groups' => User::getGroups(),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->isSuperadmin() && !Yii::$app->user->isSuperadmin()) {
            throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');
        }

        Yii::$app->authManager->revokeAll($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Пользователь успешно удален');

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
