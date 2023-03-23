<?php $this->setLayoutVar('title', $status['user_name']); ?>
<?php echo $this->render('status/status', ['status' => $status]); ?>
