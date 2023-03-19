<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionDecorationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute('session', new Session());

        return $handler->handle($request);
    }
}
