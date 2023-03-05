<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var \Psr\Http\Message\ServerRequestInterface $request
 * @var string $baseUrl
 * @var ?string $title
 */
/** @var \O0h\KantanFw\Http\Session $session */
$session = $request->getAttribute('session');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if (isset($title)): echo $this->escape($title) . ' - '; endif; ?>Mini Blog</title>
    <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen">
</head>
<body>

<div id="header">
    <h1><a href="<?= $baseUrl ?>/">Mini Blog</a></h1>
</div>

<div id="nav">
    <p>
        <?php if ($session && $session->isAuthenticated()): ?>
        <a href="<?= $baseUrl; ?>/">ホーム</a>
        <a href="<?= $baseUrl; ?>/account">アカウント</a>
        <?php else: ?>
        <a href="<?= $baseUrl; ?>/signin">ログイン</a>
        <a href="<?= $baseUrl; ?>/account/signup">アカウント登録</a>
        <?php endif; ?>
    </p>
</div>
<div id="main">
    <?= $_content; ?>
</div>
</body>
</html>
