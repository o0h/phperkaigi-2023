<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Action\ActionAwareTrait;
use O0h\KantanFw\Http\Action\ErrorAction;
use O0h\KantanFw\Http\Exception\RedirectException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    use ActionAwareTrait;

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ContainerInterface $container
    ) {
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (RedirectException $e) {
            xdebug_break();
            return $this->responseFactory->createResponse($e->getCode(), )
                ->withHeader('Location', $e->redirectTo ?? '/');
        } catch (\Throwable $e) {
            /** @var ErrorAction $action */
            $action = $this->resolveAction(ErrorAction::class);
            $action->setRequest($request);
            $action->setException($e);

            return $action();
        }
    }
}
