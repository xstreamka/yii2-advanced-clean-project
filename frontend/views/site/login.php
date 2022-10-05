<?php

/**
 * @var yii\web\View $this
 * @var yii\bootstrap5\ActiveForm $form
 * @var \common\models\LoginForm $model
 */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Для входа заполните следующие поля:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div class="my-1 mx-0" style="color:#999;">
                    Если вы забыли пароль, вы можете <?= Html::a('сбросить его', ['site/request-password-reset']) ?>.
                    <br>
                    Необходимо новое письмо для подтверждения электронной почты? <?= Html::a('Отправить', ['site/resend-verification-email']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
