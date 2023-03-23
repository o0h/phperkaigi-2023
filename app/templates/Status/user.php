<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var array<string, mixed> $user
 * @var bool $following
 * @var string $baseUrl
 * @var string $_token
 * @var array $statuses
 */
$this->setLayoutVar('title', $user['user_name']);
?>
<h2><?= $this->escape($user['user_name']); ?></h2>

<?php if (!is_null($following)): ?>
<?php if ($following): ?>
<p>フォローしています</p>
<?php else: ?>
<form action="<?= $baseUrl; ?>/follow" method="post">
	<input type="hidden" name="_token" value="<?= $this->escape($_token); ?>" />
	<input type="hidden" name="following_name" value="<?= $this->escape($user['user_name']); ?>" />
	<input type="submit" value="フォローする"/>
</form>
<?php endif; ?>
<?php endif; ?>
<div id="statuses">
    <?php foreach ($statuses as $status): ?>
    <?= $this->render('status/_status', ['status' => $status]); ?>
    <?php endforeach; ?>
</div>
