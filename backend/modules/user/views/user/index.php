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

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\UserSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var array $groups
 */

$this->title = 'Пользователи';

$this->params['breadcrumbs'][] = $this->title;

?>

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
                return Html::a($data->username, ['view', 'id'=> $data->id]);
            }],
	        ['attribute' => 'lastname', 'content' => function ($data) {
		        /** @var common\models\User $data */
		        return Html::a($data->lastname, ['view', 'id'=> $data->id]);
	        }],
            'email:email',
            [
                'attribute' => 'group',
                'filter' => $groups,
                'content' => function ($data) {
                    /** @var common\models\User $data */
                    $str = '<ul>';
	                foreach ($data->group as $groupName => $group) {
                        $str .= Html::tag('li', Html::a($groupName, ['/user/role/update', 'alias' => $group]));
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
            ]
        ],
    ]); ?>
</div>
