<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var string $baseUrl
 * @var string $_token
 * @var string[] $errors
 * @var string $body
 * @var string[] $statuses
 */
$this->setLayoutVar('title', 'ホーム');
?>
<h2>ホーム</h2>
<form action="<?= $baseUrl; ?>/status/post" method="post">
    <input type="hidden" name="_token" value="<?= $this->escape($_token) ?>" />

    <?php if(isset($errors) && count($errors) > 0): ?>
        <?= $this->render('errors', ['errors' => $errors]); ?>
    <?php endif; ?>

    <textarea name="body" rows="2" cols="60"><?= $this->escape($body) ?></textarea>

    <p>
        <input type="submit" value="発言"/>
    </p>
</form>

<div id="statuses">
    <?php foreach($statuses as $status): ?>
        <?= $this->render('status', ['status' => $status]); ?>
    <?php endforeach; ?>
</div>
