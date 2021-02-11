<?php

use common\helpers\CF;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model common\models\User
 * @var $form yii\widgets\ActiveForm
 * @var array $groups
 */

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Иван']) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'placeholder' => 'Иванов']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'i.ivanov@agatgroup.com']) ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
    <?php } else { ?>
        <div class="form-group field-user-password">
            <label class="control-label" for="user-password">Новый пароль</label>
            <?= Html::input('password', 'password', null, ['class' => 'form-control', 'id' => 'user-password']) ?>
            <div class="help-block"></div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'status')->dropDownList(User::STATUS_TEXT, CF::getSelectpickerOptions()) ?>

    <?= $form->field($model, 'group')->dropDownList($groups, CF::getSelectpickerOptions(true)) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
