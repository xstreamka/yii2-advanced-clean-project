<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\rbac\Permission;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\UserSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Разрешения';

$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user']];
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/user/role']];

if (!empty($filter)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::$app->authManager->getRole($filter)->description, 'url' => ['/user/role/update', 'alias' => Yii::$app->authManager->getRole($filter)->name]];
}

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (empty(Yii::$app->request->get('filter'))) { ?>
            <?= Html::a('Создать разрешение', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } else { ?>
            <?= Html::a('Изменить', ['/user/role/update', 'alias' => Yii::$app->request->get('filter')], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Все', ['/user/permission'], ['class' => 'btn btn-default']) ?>
        <?php } ?>
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
			        return Html::a($data->name, Url::toRoute(['/user/permission/update', 'alias' => $data->name]));
		        }
	        ],
            ['label' => 'Название', 'attribute' => 'description'],
            ['label' => 'Создан', 'content' => function ($data) {
                return date('d.m.Y', $data->createdAt);
            }],
            ['label' => 'Обновлен', 'content' => function ($data) {
                return date('d.m.Y (H:i:s)', $data->updatedAt);
            }],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Permission $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'alias' => $model->name]);
                 },
            ]
        ],
    ]);
    ?>

</div>