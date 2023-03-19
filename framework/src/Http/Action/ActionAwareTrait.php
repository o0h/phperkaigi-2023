<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Action;

trait ActionAwareTrait
{
    /**
     * @param class-string<Action> $action
     * @return Action
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function resolveAction(string $action): Action
    {
        /** @var Action $action */
        $action = $this->container->get($action);
        $this->injectActionDependencies($action);

        return $action;
    }

    /**
     * @param Action $action
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function injectActionDependencies(mixed $action): void
    {
        /**
         * PHP-DIのワイルドカードを使うと、単純にContainer::get()で解決可能
         * \O0h\KantanFw\Http\Action::class => DI\create('App\Action\*Action'),
         */
        $actionReflection = new \ReflectionClass($action);
        if (!$actionReflection->hasMethod('depends')) {
            return;
        }
        assert(method_exists($action, 'depends'));

        $depends = new \ReflectionMethod($action, 'depends');
        $dependencies = [];
        foreach ($depends->getParameters() as $parameter) {
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
        if (!$dependencies) {
            return ;
        }

        $action->depends(...$dependencies);
    }
}
