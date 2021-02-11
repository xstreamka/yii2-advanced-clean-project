<?php

/**
 * @var $this yii\web\View
 * @var string $content
 */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\helpers\Menu;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$user = Yii::$app->user;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<?php $this->registerCsrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
if ($user->can('alert')) {
    echo Alert::widget();
}
?>
<div class="site-wrap">

	<div class="site-canvas">

		<!-- header site -->
		<header class="main-header">

			<!-- header menu -->
            <?php echo Menu::widget()?>
			<!-- END header menu -->

		</header>

        <div class="content">

	        <?php if (!empty($this->params['breadcrumbs'])) { ?>
                <div class="breadcrumb-wrap mp-elem-bottom">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 text-left">

						        <?= Breadcrumbs::widget([
							        'links' => $this->params['breadcrumbs'] ?? [],
							        'homeLink' => ['label' => 'Главная', 'url' => Yii::$app->homeUrl],
						        ]) ?>

                            </div>
                        </div>
                    </div>
                </div>
	        <?php } ?>

            <?= $content ?>

        </div>

        <div class="bg_footer">
			<div class="footer">
				<div class="container">

				</div>
			</div>
		</div>

	</div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
