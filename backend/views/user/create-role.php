<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use common\helpers\CF;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $type string
 */

$this->title = ($type == 'new' ? 'Создать' : 'Изменить') . ' группу';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['roles']];
$this->params['breadcrumbs'][] = $this->title;

$message = $message ?? Yii::$app->request->get('message') ?? null;

if (!empty(Yii::$app->request->get('message'))) {
    $this->registerJs('history.replaceState("", "", "' . urldecode(Url::toRoute(Yii::$app->requestedRoute . '/' . $model->name)) . '")');
}
?>

<?php if (!empty($message)) { ?>
    <div class="alert alert-<?php echo $message['status'];?>">
        <p><?php echo $message['text'];?></p>
    </div>
<?php } ?>

<?php if (!empty($message) && $message == 'edit') { ?>
    <div class="alert alert-success">
        <p>Группа успешно изменена</p>
    </div>
<?php } ?>

<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'description') ?>

	        <?= $form->field($model, 'permissions')->dropDownList($permissions, CF::getSelectpickerOptions(true, false, true, true)) ?>

            <div class="form-group">
                <?= Html::submitButton($type == 'new' ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary', 'name' => 'default-button']) ?>
                <?= Html::a('Отмена', ['roles'], ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php /* ?>
echo Html::beginForm('', 'post');
echo '<p>Код</p>';
echo Html::input('text', 'Role[name]');
echo '<p>Название</p>';
echo Html::input('text', 'Role[description]');
echo '<br>';
echo Html::submitButton('Создать', ['class' => 'btn-primary']);
echo Html::endForm();
<?php */ ?>