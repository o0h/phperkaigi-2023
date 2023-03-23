<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

$actions = (function () {
    $map = [];

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(APP_ROOT . '/src/Action/'),
        RecursiveIteratorIterator::LEAVES_ONLY,
    );
    foreach ($files as $file) {
        if ($file->isDir()) {
            continue;
        }
        $file = $file->getPathName();
        if (!str_ends_with($file, 'Action.php')) {
            continue;
        }
        $class = str_replace(
            '/',
            '\\',
            str_replace(APP_ROOT . '/src', 'App', substr($file, 0, -4))
        );
        $map[$class] = function (ContainerInterface $container) use ($class) {
            return new $class(
                $container->get(\Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(\Psr\Http\Message\StreamFactoryInterface::class),
            );
        };
    }
    return $map;
})();

return new \O0h\KantanFw\DI\Container([
    ...$actions,
        \Psr\Http\Message\ServerRequestInterface::class => fn () => O0h\KantanFw\Http\Message\ServerRequestFactory::fromGlobals(),
        \Psr\Http\Message\ResponseFactoryInterface::class => fn () => new \O0h\KantanFw\Http\Message\ResponseFactory(),
        \Psr\Http\Message\StreamFactoryInterface::class => fn () => new O0h\KantanFw\Http\Message\StreamFactory(),
        \O0h\KantanFw\Http\Emitter::class => fn () => new \O0h\KantanFw\Http\Emitter(),
        \O0h\KantanFw\Http\Action\ErrorAction::class => function (ContainerInterface  $container) {
            return new \O0h\KantanFw\Http\Action\ErrorAction(
                $container->get(\Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(\Psr\Http\Message\StreamFactoryInterface::class),
            );
        },

        // middlewares
        \O0h\KantanFw\Http\Middleware\CsrfGuardMiddleware::class => fn () => new \O0h\KantanFw\Http\Middleware\CsrfGuardMiddleware(),
        \O0h\KantanFw\Http\Middleware\ErrorHandlerMiddleware::class => function (ContainerInterface   $container) {
            return new \O0h\KantanFw\Http\Middleware\ErrorHandlerMiddleware(
                $container->get(\Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(\O0h\KantanFw\Http\Router\ActionResolver::class),
            );
        },
        \O0h\KantanFw\Http\Middleware\RoutingMiddleware::class => function (ContainerInterface   $container) {
            return new \O0h\KantanFw\Http\Middleware\RoutingMiddleware(
                $container->get(O0h\KantanFw\Http\Router\Router::class),
                $container->get(\Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(\O0h\KantanFw\Http\Router\ActionResolver::class),
            );
        },
        \O0h\KantanFw\Http\Middleware\SessionDecorationMiddleware::class => fn () => new \O0h\KantanFw\Http\Middleware\SessionDecorationMiddleware(),

        \O0h\KantanFw\Http\Router\ActionResolver::class => function (ContainerInterface   $container) {
            return new \O0h\KantanFw\Http\Router\ActionResolver($container);
        },
        \App\Application::class . '|singleton' => function (ContainerInterface   $container) {
            $emitter = $container->get(\O0h\KantanFw\Http\Emitter::class);
            $container = $container->get(ContainerInterface  ::class);
            return new \App\Application($container, $emitter);
        },
        \O0h\KantanFw\Http\Router\Router::class . '|singleton' => fn () => new \O0h\KantanFw\Http\Router\Router(require_once APP_ROOT . '/config/routes.php'),
        \O0h\KantanFw\Database\Manager::class => function () {
            $manager = new \O0h\KantanFw\Database\Manager();
            $manager->connect('default', [
                'dsn' => getenv('DATABASE_DSN'),
                'user' => getenv('DATABASE_USER'),
                'password' => getenv('DATABASE_PASSWORD'),
            ]);
            return $manager;
        },

        // repositories
        \App\Repository\StatusRepository::class => function (ContainerInterface $container) {
            /** @var \O0h\KantanFw\Database\Manager $dbManager */
            $dbManager = $container->get(\O0h\KantanFw\Database\Manager::class);
            $connection = $dbManager->getConnection('default');
            return new \App\Repository\StatusRepository($connection);
        },
        \App\Repository\UserRepository::class => function (ContainerInterface $container) {
            /** @var \O0h\KantanFw\Database\Manager $dbManager */
            $dbManager = $container->get(\O0h\KantanFw\Database\Manager::class);
            $connection = $dbManager->getConnection('default');
            return new \App\Repository\UserRepository($connection);
        },
        \App\Repository\FollowingRepository::class => function (ContainerInterface $container) {
            /** @var \O0h\KantanFw\Database\Manager $dbManager */
            $dbManager = $container->get(\O0h\KantanFw\Database\Manager::class);
            $connection = $dbManager->getConnection('default');
            return new \App\Repository\FollowingRepository($connection);
        },
]);
