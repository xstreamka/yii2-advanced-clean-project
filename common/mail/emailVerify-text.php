<?php

/**
 * @var yii\web\View $this
 * @var common\models\User $user
 */

$verifyLink = $user->getVerifyEmailLink();

?>

Здравствуйте, <?php echo $user->name; ?>!

Перейдите по ссылке ниже, чтобы подтвердить свою электронную почту:

<?php echo $verifyLink; ?>
