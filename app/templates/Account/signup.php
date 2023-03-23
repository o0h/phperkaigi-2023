<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var string $baseUrl
 * @var string $_token
 * @var string[] $errors
 * @var string $userName
 * @var string $password
 */
$this->setLayoutVar('title', 'アカウント登録')
?>

<h2>アカウント登録</h2>

<form action="<?= $baseUrl; ?>/account/register" method="post">
	<input type="hidden" name="_token" value="<?= $this->escape($_token) ?>" />

    <?php if(isset($errors) && count($errors) > 0): ?>
        <?= $this->render('errors', ['errors' => $errors]); ?>
    <?php endif; ?>


    <?= $this->render('account/inputs', [
        'userName' => $userName, 'password' => $password,
    ]); ?>
	<p>
		<input type="submit" value="登録" />
	</p>
</form>
