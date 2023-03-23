<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var string $baseUrl
 * @var array{user_name: string, body: string, created_at: string} $status
 */
$this->setLayoutVar('title', 'ホーム');
?>
<div class="status">
    <div class="status_content">
        <a href="<?= $baseUrl ?>/user/<?= $this->escape($status['user_name']) ?>">
            <?= $this->escape($status['user_name']) ?>
        </a>
        <?= $this->escape($status['body']) ?>
    </div>
    <div>
        <a href="<?= $baseUrl ?>/user/<?= $this->escape($status['user_name']) ?>/status/<?= $this->escape($status['id']); ?>">
            <?= $this->escape($status['created_at']) ?>
        </a>
    </div>
</div>
