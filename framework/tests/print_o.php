<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__, 2) . '/app/vendor/autoload.php';
require dirname(__DIR__, 2) . '/app/config/bootstrap.php';

$container = require dirname(__DIR__, 2) . '/app/config/di.php';
$app = $container->get(\App\Application::class);

print_o($app);