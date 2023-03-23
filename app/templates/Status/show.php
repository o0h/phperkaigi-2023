<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var array{user_name: string, body: string} $status
 */
$this->setLayoutVar('title', $status['user_name']);
?>
<?= $this->render('Status/_status', ['status' => $status]); ?>