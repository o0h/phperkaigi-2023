<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var string $baseUrl
 * @var string $_token
 * @var string[] $errors
 * @var string $userName
 * @var string $password
 */
$this->setLayoutVar('title', 'ログイン');
?>
<h2>ログイン</h2>

<p>
    <a href="<?= $baseUrl; ?>/account/signup">新規ユーザ登録</a>
</p>

<form action="<?= $baseUrl; ?>/account/authenticate" method="post">
    <input type="hidden" name="_token" value="<?= $this->escape($_token); ?>" />

    <?php if (isset($errors) && count($errors) > 0): ?>
        <?= $this->render('errors', compact('errors')); ?>
    <?php endif; ?>

    <?= $this->render('account/inputs', [
        'userName' => $userName, 'password' => $password,
    ]); ?>

    <p>
        <input type="submit" value="ログイン" />
    </p>
</form>