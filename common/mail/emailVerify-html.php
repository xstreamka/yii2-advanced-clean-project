<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\User $user
 */

$verifyLink = $user->getVerifyEmailLink();

?>

<div class="verify-email">
    <p>Здравствуйте, <?php echo Html::encode($user->name) ?>!</p>

    <p>Перейдите по ссылке ниже, чтобы подтвердить свою электронную почту:</p>

    <p><?php echo Html::a(Html::encode($verifyLink), $verifyLink); ?></p>
</div>
