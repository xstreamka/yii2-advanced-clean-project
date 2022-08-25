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

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true, 'placeholder' => 'Иванов']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'i.ivanov@agatgroup.com']) ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
    <?php } else { ?>
        <?= $form->field($model, 'password_new')->passwordInput() ?>
    <?php } ?>

    <?= $form->field($model, 'status')->dropDownList(User::STATUS_TEXT, CF::getSelectpickerOptions()) ?>

    <?= $form->field($model, 'group')->dropDownList($groups, CF::getSelectpickerOptions(false, true)) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
