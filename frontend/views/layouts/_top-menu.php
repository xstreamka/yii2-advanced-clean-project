<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 13.07.2020
 * Time: 9:05
 */

/**
 * @var $this yii\web\View
 * @var array $menuItems
 */

use common\helpers\UriHelper;
use yii\helpers\Html;

?>

<div class="main-header__menu-wrap navbar-collapse collapse" id="navbar-main" role="navigation"
     data-scroll-lock-scrollable>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<nav class="navbar main-header__menu main-header__menu--new">
                    <ul class="nav navbar-nav">
                        <?php
                        foreach ($menuItems as $menuItem) {
                            echo Html::tag('li', Html::a($menuItem['label'], $menuItem['url'], $menuItem['options'] ?? []), ['class' => $menuItem['url'] === UriHelper::getCleanUrl() ? 'active' : '']);
                        }
                        ?>
                    </ul>
				</nav>
			</div>
		</div>
	</div>
</div>
