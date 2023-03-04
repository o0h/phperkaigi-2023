<?php

declare(strict_types=1);

namespace O0h\KantanFw;

use O0h\KantanFw\Http\Emitter;
use O0h\KantanFw\Http\Middleware\AuthMiddleware;
use O0h\KantanFw\Http\Middleware\ErrorHandlerMiddleware;
use O0h\KantanFw\Http\Middleware\RoutingMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Application implements RequestHandlerInterface
{
    public function __construct(
        private ContainerInterface $container,
        private readonly Emitter $emitter
    ) {
    }

    /**
     * @var class-string<MiddlewareInterface>[]
     */
    protected $middlewares = [
        ErrorHandlerMiddleware::class,
        AuthMiddleware::class,
        RoutingMiddleware::class
    ];

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (count($this->middlewares) === 0) {
            xdebug_break();
        }
        $middleware = array_shift($this->middlewares);

        /** @var MiddlewareInterface $next */
        $next = $this->container->get($middleware);

        return $next->process($request, $this);
    }

    public function run(ServerRequestInterface $serverRequest): void
    {
        $response = $this->handle($serverRequest);

        $this->emitter->emit($response);
    }
}
