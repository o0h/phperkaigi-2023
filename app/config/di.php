<?php

declare(strict_types=1);

use Laminas\Diactoros\ServerRequestFactory;
use O0h\KantanFw\Http\Emitter;
use O0h\KantanFw\View\View;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

return new \DI\Container(
    [
        \App\Application::class => DI\autowire(\App\Application::class),
        ContainerInterface::class  => DI\factory(fn () => $this),
        ServerRequestInterface::class => DI\factory(fn () => ServerRequestFactory::fromGlobals()),
        \Psr\Http\Message\ResponseFactoryInterface::class => DI\factory(fn () => new \Laminas\Diactoros\ResponseFactory()),
        \Psr\Http\Message\StreamFactoryInterface::class => DI\factory(fn () => new \Laminas\Diactoros\StreamFactory()),
        Emitter::class => DI\factory(fn () => new Emitter()),
        View::class => DI\factory(fn () => new View(TEMPLATE_PATH)),
        \O0h\KantanFw\Http\Router::class => fn () => new \O0h\KantanFw\Http\Router(require_once APP_ROOT . '/config/routes.php'),
        // \O0h\KantanFw\Http\Action::class => DI\create('App\Action\*Action'),
    ]
);
