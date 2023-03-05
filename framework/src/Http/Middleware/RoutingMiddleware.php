<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Action;
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
    public function __construct(private readonly Router $router, private readonly ContainerInterface $container)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->router->resolve($request);
        /** @var class-string<Action> $action */
        $action = $request->getAttribute('action');
        if (!$action) {
            throw new NotFoundException();
        }
        $action = $this->resolveAction($action);
        $action->setRequest($request);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);
        $action->setResponse($responseFactory->createResponse());

        $args = $request->getAttribute('args');

        return $action(...$args);
    }

    /**
     * @param class-string<Action> $action
     * @return Action
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function resolveAction(string $action): Action
    {
        /**
         * PHP-DIのワイルドカードを使うと、単純にContainer::get()で解決可能
         * \O0h\KantanFw\Http\Action::class => DI\create('App\Action\*Action'),
         */
        $constructor = new \ReflectionMethod($action, '__construct');
        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            if ($parameter->isOptional()) {
                $value = $parameter->getDefaultValue();
                $dependencies[$name] = $value;
                continue;
            }
            $class = $parameter->getType()->getName();
            if ($this->container->has($class)) {
                $value = $this->container->get($class);
            } else {
                $value = new $class();
            }
            $dependencies[$name] = $value;
        }

        $action = new $action(...$dependencies);

        return $action;
    }
}
