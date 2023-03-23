<?php

declare(strict_types=1);

namespace App;

use O0h\KantanFw\Http\Middleware\AuthMiddleware;
use O0h\KantanFw\Http\Middleware\CsrfGuardMiddleware;
use O0h\KantanFw\Http\Middleware\ErrorHandlerMiddleware;
use O0h\KantanFw\Http\Middleware\RoutingMiddleware;
use O0h\KantanFw\Http\Middleware\SessionDecorationMiddleware;

class Application extends \O0h\KantanFw\Application
{
    protected array $middlewares = [
        ErrorHandlerMiddleware::class,
        SessionDecorationMiddleware::class,
        CsrfGuardMiddleware::class,
        RoutingMiddleware::class,
    ];
}
