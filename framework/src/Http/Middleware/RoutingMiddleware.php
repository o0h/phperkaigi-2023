<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Action\ActionAwareTrait;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Router\ActionResolver;
use O0h\KantanFw\Http\Router\Router;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ActionResolver $actionResolver
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->resolve($request);
        if (!$route) {
            throw new NotFoundException();
        }
        /** @var class-string<Action> $action */
        $action = $route['action'];
        $action = $this->actionResolver->resolve($action);
        assert(is_callable($action));

        $request = $request->withAttribute('baseUrl', $this->getBaseUrl($request));

        $action->setRequest($request);
        $action->setResponse($this->responseFactory->createResponse());

        $action->filterAuth();

        return $action(...$route['args']);
    }

    private function getBaseUrl(ServerRequestInterface $request): string
    {
        $scriptName = $request->getServerParams()['SCRIPT_NAME'] ?? '/index.php';
        $requestUri = $request->getServerParams()['REQUEST_URI'] ?? '/';

        return match (true) {
            str_starts_with($requestUri, $scriptName) => $scriptName,
            str_starts_with($requestUri, dirname($scriptName)) => rtrim(dirname($scriptName), '/'),
            default => ''
        };
    }
}
