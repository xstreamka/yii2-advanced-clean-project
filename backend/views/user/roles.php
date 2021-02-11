<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use common\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Группы';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$message = $message ?? Yii::$app->request->get('message') ?? null;

if (!empty(Yii::$app->request->get('message'))) {
    $this->registerJs('history.replaceState("", "", "' . urldecode(Url::toRoute(Yii::$app->requestedRoute)) . '")');
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
        <?= Html::a('Создать группу', ['create-role'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Код',
                'content' => function ($data) {
                    return Html::a($data->name, Url::toRoute(['/user/update-role/', 'alias' => $data->name]));
                }
            ],
            ['label' => 'Название', 'attribute' => 'description'],
            ['label' => 'Разрешений', 'content' => function ($data) {
                $permissions = Yii::$app->authManager->getPermissionsByRole($data->name);
                return Html::a(count($permissions), Url::toRoute(['/user/permission', 'filter' => $data->name]));
            }],
            ['label' => 'Пользователей', 'content' => function ($data) {
                $role_users = Yii::$app->authManager->getUserIdsByRole($data->name);
                foreach ($role_users as $user) {
                    if (!User::find()->where(['id' => $user])->one()) {
                        Yii::$app->authManager->revokeAll($user);
                    }
                }
                return Html::a(count($role_users), Url::toRoute(['/user/index', 'UserSearch[group]' => $data->name]));
            }],
            ['label' => 'Создан', 'content' => function ($data) {
                return date('d.m.Y', $data->createdAt);
            }],
            ['label' => 'Обновлен', 'content' => function ($data) {
                return date('d.m.Y (H:i:s)', $data->updatedAt);
            }],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $url = '/admin/user/update-role/' . $model->name;
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        return Html::a($icon, $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        $url = '/admin/user/delete-role/' . $model->name;
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]);
                        return Html::a($icon, $url, ['data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']);
                    }
                ]
            ]
        ],
    ]);
    ?>

</div>