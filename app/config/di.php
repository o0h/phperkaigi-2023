<?php

declare(strict_types=1);

use Laminas\Diactoros\ServerRequestFactory;
use O0h\KantanFw\Http\Emitter;
use O0h\KantanFw\View\View;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

return new \DI\Container(
    [
        ContainerInterface::class  => DI\factory(fn () => $this),
        Emitter::class => DI\factory(fn () => new Emitter()),
        ServerRequestInterface::class => DI\factory(fn () => ServerRequestFactory::fromGlobals()),
        \App\Application::class => DI\autowire(\App\Application::class),
        ResponseFactoryInterface::class => DI\factory(fn () => new \Laminas\Diactoros\ResponseFactory()),
        \Psr\Http\Message\StreamFactoryInterface::class => DI\factory(fn () => new \Laminas\Diactoros\StreamFactory()),
        \O0h\KantanFw\Http\Router::class => fn () => new \O0h\KantanFw\Http\Router(require_once APP_ROOT . '/config/routes.php'),
        // \O0h\KantanFw\Http\Action::class => DI\create('App\Action\*Action'),
        \O0h\KantanFw\Database\Manager::class => function () {
            $manager = new \O0h\KantanFw\Database\Manager();
            $manager->connect('default', [
                    'dsn' => getenv('DATABASE_DSN'),
                    'user' => getenv('DATABASE_USER'),
                    'password' => getenv('DATABASE_PASSWORD'),
            ]);
            return $manager;
        },
        \App\Repository\StatusRepository::class => DI\factory(function (\DI\Container $container) {
            /** @var \O0h\KantanFw\Database\Manager $dbManager */
            $dbManager = $container->get(\O0h\KantanFw\Database\Manager::class);
            $connection = $dbManager->getConnection('default');
            return new \App\Repository\StatusRepository($connection);
        }),
        \App\Repository\UserRepository::class => DI\factory(function (\DI\Container $container) {
            /** @var \O0h\KantanFw\Database\Manager $dbManager */
            $dbManager = $container->get(\O0h\KantanFw\Database\Manager::class);
            $connection = $dbManager->getConnection('default');
            return new \App\Repository\UserRepository($connection);
        }),
        \O0h\KantanFw\Http\Action\ErrorAction::class => DI\autowire(\O0h\KantanFw\Http\Action\ErrorAction::class),
    ]
);
