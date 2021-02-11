<?php
namespace backend\controllers;

use common\helpers\LogHelper;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
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
		                'actions' => ['index', 'logout', 'error'],
		                'allow' => true,
		                'roles' => ['@'],
	                ],
	                [
		                'actions' => ['index', 'view'],
		                'allow' => true,
		                'roles' => ['admin'],
	                ],
	                [
		                'allow' => true,
		                'roles' => ['superadmin', 'admin']
	                ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
	                'logout' => ['post', 'get']
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
	    if (!Yii::$app->user->can('dashboard')) {
		    return $this->redirect('/');
	    }

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
        	if ($model->login()) {
		        LogHelper::auth(LogHelper::MESSAGE_AUTH_SUCCESS);
		        return $this->goBack();
	        } else {
		        $message = [
			        'title' => LogHelper::MESSAGE_AUTH_ERROR,
			        'email' => $model->email,
			        'password' => $model->password,
			        'error' => $model->errors
		        ];
		        LogHelper::auth($message, LogHelper::TYPE_ERROR);
		        $model->password = '';
	        }
        }

	    return $this->render('login', [
		    'model' => $model,
	    ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
