<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$message = $message ?? Yii::$app->request->get('message') ?? null;

if (!empty(Yii::$app->request->get('message'))) {
    $this->registerJs('history.replaceState("", "", "' . urldecode(Url::toRoute(Yii::$app->requestedRoute)) . '")');
}

$roles = Yii::$app->authManager->getRoles();
$dropdown_items = [];
foreach ($roles as $role) {
    if ($role->name != 'superadmin') {
        $dropdown_items[$role->name] = $role->description;
    }
}
?>

<?php if (!empty($message)) { ?>
    <div class="alert alert-<?php echo $message['status'];?>">
        <p><?php echo $message['text'];?></p>
    </div>
<?php } ?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <?php if (!empty(Yii::$app->request->get())) { ?>
	        <?= Html::a('Сбросить фильтр', ['index'], ['class' => 'btn btn-info']) ?>
        <?php } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            ['attribute' => 'username', 'content' => function ($data) {
                /** @var common\models\User $data */
                return Html::a($data->username, ['user/view', 'id'=> $data->id]);
            }],
	        ['attribute' => 'last_name', 'content' => function ($data) {
		        /** @var common\models\User $data */
		        return Html::a($data->last_name, ['user/view', 'id'=> $data->id]);
	        }],
            'email:email',
            [
                'attribute' => 'group',
                'filter' => $dropdown_items,
                'content' => function ($data) {
                    /** @var common\models\User $data */
                    $str = '<ul>';
	                foreach ($data->group as $groupName => $group) {
                        $str .= Html::tag('li', Html::a($groupName, ['user/update-role/' . $group]));
                    }
                    return $str;
                }
            ],
            [
                'attribute' => 'status',
                'filter' => User::STATUS_TEXT,
                'content' => function ($data) {
                    /** @var common\models\User $data */
                    return $data->statusText;
                }
            ],
            'created_at:date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}'
            ]
        ],
    ]); ?>
</div>
