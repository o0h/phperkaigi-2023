<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Router;

use O0h\KantanFw\Http\Action\Action;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class ActionResolver
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @phpstan-param class-string<Action> $action
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resolve(string $action): Action
    {
        /** @var Action $instance */
        $instance = $this->container->get($action);
        $this->injectActionDependencies($instance);

        return $instance;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function injectActionDependencies(Action $action): void
    {
        /**
         * PHP-DIのワイルドカードを使うと、単純にContainer::get()で解決可能
         * \O0h\KantanFw\Http\Action::class => DI\create('App\Action\*Action'),
         */
        $actionReflection = new ReflectionClass($action);
        if (!$actionReflection->hasMethod('depends')) {
            return;
        }
        assert(method_exists($action, 'depends'));

        $depends = new ReflectionMethod($action, 'depends');
        $dependencies = [];
        foreach ($depends->getParameters() as $parameter) {
            $name = $parameter->getName();
            if ($parameter->isOptional()) {
                continue;
            }
            assert($parameter->getType() instanceof ReflectionNamedType);
            $class = $parameter->getType()->getName();
            if ($this->container->has($class)) {
                $value = $this->container->get($class);
            } else {
                $value = new $class();
            }
            $dependencies[$name] = $value;
        }

        $action->depends(...$dependencies);
    }
}
