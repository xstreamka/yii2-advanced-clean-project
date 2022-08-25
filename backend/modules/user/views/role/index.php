<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\UserSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Группы';

$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
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
                    return Html::a($data->name, Url::toRoute(['update', 'alias' => $data->name]));
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
                return Html::a(count($role_users), Url::toRoute(['/user', 'UserSearch[group]' => $data->name]));
            }],
            ['label' => 'Создан', 'content' => function ($data) {
                return date('d.m.Y', $data->createdAt);
            }],
            ['label' => 'Обновлен', 'content' => function ($data) {
                return date('d.m.Y (H:i:s)', $data->updatedAt);
            }],
            [
                'class' => \yii\grid\ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'alias' => $model->name]);
                },
            ]
        ],
    ]);
    ?>

</div>
