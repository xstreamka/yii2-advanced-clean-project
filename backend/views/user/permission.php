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
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Разрешения';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['roles']];
if (!empty($filter)) $this->params['breadcrumbs'][] = ['label' => Yii::$app->authManager->getRole($filter)->description, 'url' => ['user/update-role/' . Yii::$app->authManager->getRole($filter)->name]];
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
        <?php if (empty(Yii::$app->request->get('filter'))) { ?>
            <?= Html::a('Создать разрешение', ['create-permission'], ['class' => 'btn btn-success']) ?>
        <?php } else { ?>
            <?= Html::a('Изменить', ['user/update-role/' . Yii::$app->request->get('filter')], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Все', ['permission'], ['class' => 'btn btn-default']) ?>
        <?php } ?>
    </p>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['label' => 'Код', 'attribute' => 'name'],
            ['label' => 'Название', 'attribute' => 'description'],
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
                        $url = '/admin/user/update-permission/' . $model->name;
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        return Html::a($icon, $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        $url = '/admin/user/delete-permission/' . $model->name;
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]);
                        return Html::a($icon, $url, ['data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']);
                    }
                ]
            ]
        ],
    ]);
    ?>

</div>