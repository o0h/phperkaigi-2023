<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';
// $container = new \O0h\KantanFw\DI\Container();
$container = require APP_ROOT . '/config/di.php';
assert($container instanceof \Psr\Container\ContainerInterface);

/** @var \O0h\KantanFw\Application $app */
$app = $container->get(\App\Application::class);
$app->run($container->get(\Psr\Http\Message\ServerRequestInterface::class));
