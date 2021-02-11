<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$message = $message ?? Yii::$app->request->get('message') ?? null;

if (!empty(Yii::$app->request->get('message'))) {
    $this->registerJs('history.replaceState("", "", "' . urldecode(Url::toRoute(Yii::$app->requestedRoute . '/?id=' . $model->id)) . '")');
}
?>

<?php if (!empty($message)) { ?>
    <div class="alert alert-<?php echo $message['status'];?>">
        <p><?php echo $message['text'];?></p>
    </div>
<?php } ?>

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
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'last_name',
	        'email:email',
	        [
		        'attribute' => 'group',
		        'format' => 'raw',
		        'value' => function ($model) {
			        /** @var common\models\User $model */
			        $str = '<ul>';
			        foreach ($model->group as $groupName => $group) {
				        $str .= Html::tag('li', Html::a($groupName, ['user/update-role/' . $group]));
			        }
			        return $str;
		        }
	        ],
	        [
		        'attribute' => 'status',
		        'value' => $model->statusText
	        ],
	        'created_at:date',
	        'updated_at:datetime',
        ],
    ]) ?>

</div>
