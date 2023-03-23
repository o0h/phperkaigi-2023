<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Action\ActionAwareTrait;
use O0h\KantanFw\Http\Action\ErrorAction;
use O0h\KantanFw\Http\Exception\RedirectException;
use O0h\KantanFw\Http\Router\ActionResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ActionResolver $actionResolver,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (RedirectException $e) {
            return $this->responseFactory->createResponse($e->getCode(), )
                ->withHeader('Location', $e->redirectTo ?? '/');
        } catch (Throwable $e) {
            /** @var ErrorAction $action */
            $action = $this->actionResolver->resolve(ErrorAction::class);
            $action->setRequest($request);
            $action->setException($e);

            return $action();
        }
    }
}
