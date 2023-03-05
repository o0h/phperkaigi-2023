<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Action\ActionAwareTrait;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{
    use ActionAwareTrait;

    public function __construct(
        private readonly Router $router,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ContainerInterface $container)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->router->resolve($request)
            ->withAttribute('baseUrl', $this->getBaseUrl($request));

        /** @var class-string<Action> $action */
        $action = $request->getAttribute('action');
        if (!$action) {
            throw new NotFoundException();
        }
        $action = $this->resolveAction($action);
        $action->setRequest($request);
        /** @var ResponseFactoryInterface $responseFactory */
        $action->setResponse($this->responseFactory->createResponse());

        $args = $request->getAttribute('args');

        return $action(...$args);
    }

    public function getBaseUrl(ServerRequestInterface $request)
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
