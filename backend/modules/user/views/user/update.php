<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model common\models\User
 * @var array $groups
 */

$this->title = 'Изменить пользователя: ' . $model->username;

$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';

?>

<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">

            <?= $this->render('_form', [
                'model' => $model,
	            'groups' => $groups,
            ]) ?>

        </div>
    </div>

</div>
