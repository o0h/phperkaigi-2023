<?php

declare(strict_types=1);

namespace O0h\KantanFw\DI;

use O0h\KantanFw\DI\Exception\DIException;
use O0h\KantanFw\DI\Exception\UnknownIdException;
use O0h\KantanFw\DI\Exception\UnknownStrategyException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private const KNOWN_STRATEGIES = ['factory', 'singleton'];

    private const DEFAULT_STRATEGY = 'factory';

    /** @var array<string, value-of<self::KNOWN_STRATEGIES>> */
    private array $strategyMap = [];

    /** @var array<string, callable> */
    private array $factoryMap = [];

    private array $pool = [];

    public function __construct(array $configuration)
    {
        $configuration[ContainerInterface::class . '|singleton'] = fn () => $this;
        foreach ($configuration as $key => $factory) {
            if (!str_contains($key, '|')) {
                $key .= '|' . self::DEFAULT_STRATEGY;
            }
            [$key, $strategy] = explode('|', $key);
            $this->strategyMap[$key] = $strategy;
            $this->factoryMap[$key] = $factory;
        }
    }

    /**
     * @throws DIException
     */
    public function get(string $id): mixed
    {
        $strategy = $this->strategyMap[$id] ?? false;
        if (!$strategy) {
            xdebug_break();
            throw new UnknownIdException("{$id} is not set");
        }

        if (!in_array($strategy, self::KNOWN_STRATEGIES, true)) {
            throw new UnknownStrategyException("{$strategy} is strategy that has not implemented");
        }

        return $this->$strategy($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->strategyMap);
    }

    private function singleton(string $id): mixed
    {
        if (array_key_exists($id, $this->pool)) {
            return $this->pool[$id];
        }
        $object = $this->factory($id);
        $this->pool[$id] = $object;

        return $object;
    }

    private function factory(string $id): mixed
    {
        $factory = $this->factoryMap[$id];

        return $factory($this);
    }
}
