<?php

declare(strict_types=1);

namespace O0h\KantanFw;

use O0h\KantanFw\Http\Emitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class Application implements RequestHandlerInterface
{
    /**
     * @var class-string<MiddlewareInterface>[]
     */
    protected array $middlewares = [];

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Emitter   $emitter
    ) {
    }

    public function run(ServerRequestInterface $serverRequest): void
    {
        $response = $this->handle($serverRequest);

        $this->emitter->emit($response);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        assert(count($this->middlewares) > 0);
        $middleware = array_shift($this->middlewares);

        /** @var MiddlewareInterface $next */
        $next = $this->container->get($middleware);

        return $next->process($request, $this);
    }
}
