<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 16.05.2019
 * Time: 16:33
 */

use common\helpers\CF;
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
            ['attribute' => 'username', 'content' => function (User $data) {
                return Html::a($data->username, ['view', 'id'=> $data->id]);
            }],
	        ['attribute' => 'name', 'content' => function (User $data) {
		        return Html::a($data->name, ['view', 'id'=> $data->id]);
	        }],
            ['attribute' => 'surname', 'content' => function (User $data) {
                return Html::a($data->surname, ['view', 'id'=> $data->id]);
            }],
            'email:email',
            [
                'attribute' => 'status',
                'filter' => Html::activeDropDownList($searchModel, 'status', User::STATUS_TEXT, CF::getSelectpickerOptions(null)),
                'content' => function (User $data) {
                    return $data->statusText;
                }
            ],
            [
                'attribute' => 'group',
                'filter' => Html::activeDropDownList($searchModel, 'group', $groups, CF::getSelectpickerOptions(null)),
                'content' => function (User $data) {
                    $str = '<ul>';
	                foreach ($data->group as $groupName => $group) {
                        $str .= Html::tag('li', Html::a($groupName, ['/user/role/update', 'alias' => $group]));
                    }
                    return $str;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'date',
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ]
        ],
    ]); ?>
</div>
