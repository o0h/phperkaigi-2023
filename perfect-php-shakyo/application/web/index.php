<?php
// Nginx + PHP-FPM構成にしているためフロントコントローラーがindex.phpに必ず固定されてしまいます
// そうなってもdebugモードを利用できるように、書籍のコードとは異なる手法を利用します
$inDebugMode = (function() {
    $key = 'debug-mode';
    if (($_GET[$key] ?? '') === 'debug') {
        setcookie($key, 'debug');
        return true;
    } elseif (($_GET[$key] ?? '') === 'no-debug') {
       setcookie($key, false, time() - 86400);
       return false;
    } elseif(($_COOKIE[$key] ?? '') === 'debug') {
        return true;
    }

    return false;
})();

require '../bootstrap.php';
require '../MiniBlogApplication.php';

$app = $inDebugMode ? new MiniBlogApplication(true) : new MiniBlogApplication();
$app->run();
