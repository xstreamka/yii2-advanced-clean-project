<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model common\models\User
 */

$this->title = $model->initials;

$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>

<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
        if (Yii::$app->user->isSuperadmin()) {
            echo Html::a('Назначения (rbac)', ['/rbac/assignment/view', 'id' => $model->id], ['class' => 'btn btn-success']);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'name',
            'surname',
	        'email:email',
	        'verification_email_at:datetime',
            [
                'attribute' => 'status',
                'value' => $model->statusText
            ],
	        [
		        'attribute' => 'group',
		        'format' => 'raw',
		        'value' => function ($model) {
			        $str = '<ul>';
			        foreach ($model->group as $groupName => $group) {
				        $str .= Html::tag('li', Html::a($groupName, ['/user/role/update', 'alias' => $group]));
			        }
			        return $str;
		        }
	        ],
	        'created_at:date',
	        'updated_at:datetime',
        ],
    ]) ?>

</div>
