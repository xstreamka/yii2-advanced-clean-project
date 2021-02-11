<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 23.06.2020
 * Time: 9:35
 */

namespace backend\controllers;


use backend\models\LoginForm;
use backend\models\UserRoleForm;
use common\models\User;
use common\models\UserSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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
						'actions' => ['login'],
						'allow' => true,
						'roles' => ['?'],
					],
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['index', 'view', 'update', 'create', 'delete'],
						'allow' => true,
						'roles' => ['change_user'],
					],
					[
						'actions' => ['index', 'view', 'roles', 'permission'],
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
					'delete' => ['post', 'get'],
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
		/** @var User $user */
		$user = Yii::$app->user->identity;
		if ($id == 1 && !$user->hasGroup('superadmin')) throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');

		return $this->render('view', [
			'model' => $this->findModel($id),
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
		$model->isNewRecord = true;
		if ($model->load(Yii::$app->request->post())) {
			if ($user = $model->signup()) {
				$user->group = $model->group;

				return $this->redirect(['user/index',
					'message' => ['status' => 'success', 'text' => 'Пользователь успешно создан']
				]);
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
		/** @var User $user */
		$user = Yii::$app->user->identity;
		if ($id == 1 && !$user->hasGroup('superadmin')) throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');

		$model = $this->findModel($id);
		$message = null;

		if ($model->load(Yii::$app->request->post())) {
			if (!empty(Yii::$app->request->post()['password'])) {
				$model->setPassword(Yii::$app->request->post()['password']);
			}
			if ($model->save()) {
				$model->group = Yii::$app->request->post('User')['group'] ?? [];
				Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен');
				return $this->redirect(['view',	'id' => $model->id]);
			}
		}
		return $this->render('update', [
			'model' => $model,
			'groups' => User::getGroups(),
			'message' => $message,
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
		/** @var User $user */
		$user = Yii::$app->user->identity;
		if ($id == 1) throw new ForbiddenHttpException ('Вам не разрешено производить данное действие.');

		Yii::$app->authManager->revokeAll($id);
		$this->findModel($id)->delete();
		return $this->redirect(['index',
			'message' => ['status' => 'success', 'text' => 'Пользователь успешно удален']
		]);
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

	/**
	 * Группы.
	 * @return string|\yii\web\Response
	 */
	public function actionRoles()
	{
		$roles = array_values(Yii::$app->authManager->getRoles());
		$dataProvider = new ArrayDataProvider([
			'allModels' => $roles,
		]);
		return $this->render('roles', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Создать группу.
	 * @return string|\yii\web\Response
	 * @throws \Exception
	 */
	public function actionCreateRole()
	{
		$model = new UserRoleForm();
		$auth = Yii::$app->authManager;
		$permissions = ArrayHelper::map($auth->getPermissions(), 'name', 'description');
		$message = null;
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$model->createGroup();
			$model->permissions = Yii::$app->request->post()['UserRoleForm']['permissions'] ?? [];

			return $this->redirect(Url::toRoute(['/user/roles',
				'message' => ['status' => 'success', 'text' => 'Группа успешно создана']
			]));
		}
		return $this->render('create-role', [
			'model' => $model,
			'permissions' => $permissions,
			'type' => 'new',
			'message' => $message
		]);
	}

	/**
	 * Изменить группу.
	 * @param string $alias
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdateRole($alias)
	{
		$model = new UserRoleForm();
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($alias);
		$permissions = ArrayHelper::map($auth->getPermissions(), 'name', 'description');
		$message = null;

		if (!$role) throw new NotFoundHttpException('Запрошенная страница не найдена.');

		if ($model->load(Yii::$app->request->post())) {
			if ($role->name != $model->name && User::canCreateAuth($model->name)) {
				$message = ['status' => 'danger', 'text' => 'Данный код уже используется'];
			} else {
				$model->updateGroup($role);
				$model->permissions = Yii::$app->request->post()['UserRoleForm']['permissions'] ?? [];

				return $this->redirect(['/user/update-role/' . $role->name,
					'message' => ['status' => 'success', 'text' => 'Группа успешно изменена']
				]);
			}
		}

		$model->name = $role->name;
		$model->description = $role->description;

		return $this->render('create-role', [
			'model' => $model,
			'permissions' => $permissions,
			'type' => 'update',
			'message' => $message,
		]);
	}

	/**
	 * Удаление группы.
	 * @param string $alias
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDeleteRole($alias)
	{
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($alias);

		if (!$role) throw new NotFoundHttpException('Запрошенная страница не найдена.');

		$auth->remove($role);

		return $this->redirect(['roles',
			'message' => ['status' => 'success', 'text' => 'Группа успешно удалена']
		]);
	}

	/**
	 * Разрешения.
	 * @return string|\yii\web\Response
	 */
	public function actionPermission()
	{
		$auth = Yii::$app->authManager;
		$filter = Yii::$app->request->get('filter') ?? null;
		$permission = $filter ? array_values($auth->getPermissionsByRole($filter)) : array_values($auth->getPermissions());
		$dataProvider = new ArrayDataProvider([
			'allModels' => $permission,
		]);

		return $this->render('permission', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Создать разрешение.
	 * @return string|\yii\web\Response
	 * @throws \Exception
	 */
	public function actionCreatePermission()
	{
		$model = new UserRoleForm();
		$message = null;
		if ($model->load(Yii::$app->request->post())) {
			if (User::canCreateAuth($model->name)) {
				$auth = Yii::$app->authManager;
				$new_permission = $auth->createPermission($model->name);
				$new_permission->description = $model->description;
				$auth->add($new_permission);

				return $this->redirect(Url::toRoute(['user/permission',
					'message' => ['status' => 'success', 'text' => 'Разрешение успешно создано']
				]));
			} else {
				$message = ['status' => 'danger', 'text' => 'Данный код уже используется'];
			}
		}

		return $this->render('create-permission', [
			'model' => $model,
			'type' => 'new',
			'message' => $message
		]);
	}

	/**
	 * Изменить разрешение.
	 * @param string $alias
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdatePermission($alias)
	{
		$model = new UserRoleForm();
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($alias);
		$message = null;

		if (!$permission) throw new NotFoundHttpException('Запрошенная страница не найдена.');

		if ($model->load(Yii::$app->request->post())) {
			$permissionOldName = $permission->name;
			if ($permissionOldName != $model->name && !User::canCreateAuth($model->name)) {
				$message = ['status' => 'danger', 'text' => 'Данный код уже используется'];
			} else {
				$permission->name = $model->name;
				$permission->description = $model->description;
				$auth->update($permissionOldName, $permission);

				return $this->redirect(['user/update-permission/' . $permission->name,
					'message' => ['status' => 'success', 'text' => 'Разрешение успешно изменено']
				]);
			}
		}

		$model->name = $permission->name;
		$model->description = $permission->description;

		return $this->render('create-permission', [
			'model' => $model,
			'type' => 'update',
			'message' => $message
		]);
	}

	/**
	 * Удалить разрешение.
	 * @param string $alias
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDeletePermission($alias)
	{
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($alias);

		if (!$permission) throw new NotFoundHttpException('Запрошенная страница не найдена.');

		$auth->remove($permission);

		return $this->redirect(['permission',
			'message' => ['status' => 'success', 'text' => 'Разрешение успешно удалено']
		]);
	}
}