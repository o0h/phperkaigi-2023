<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\Http\Router;

use DateTimeImmutable;
use DateTimeInterface;
use O0h\KantanFw\Database\Repository;
use O0h\KantanFw\DI\Container;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Message\ResponseFactory;
use O0h\KantanFw\Http\Message\StreamFactory;
use O0h\KantanFw\Http\Router\ActionResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SplQueue;

#[CoversClass(ActionResolver::class)]
class ActionResolverTest extends TestCase
{
    #[Test]
    public function resolve_Actionの解決(): void
    {
        $action = new class (new ResponseFactory(), new StreamFactory()) extends Action {
        };

        $container = new Container([
            $action::class => fn () => $action,
        ]);
        $subject = new ActionResolver($container);

        $actual = $subject->resolve($action::class);

        $this->assertInstanceOf($action::class, $actual);
    }

    #[Test]
    public function resolve_依存の解決(): void
    {
        $action = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            public SplQueue $queue;

            public function depends(SplQueue $queue)
            {
                $this->queue = $queue;
            }
        };

        $container = new Container([
            $action::class => fn () => $action,
        ]);
        $subject = new ActionResolver($container);

        $actual = $subject->resolve($action::class);

        $this->assertInstanceOf(SplQueue::class, $actual->queue);
    }

    #[Test]
    public function resolve_DIコンテナでの依存の解決()
    {
        $action = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            public Repository $repository;

            public function depends(Repository $repository)
            {
                $this->repository = $repository;
            }
        };

        $container = new Container([
            $action::class => fn () => $action,
            Repository::class => fn () => $this->createMock(Repository::class),
        ]);
        $subject = new ActionResolver($container);

        $actual = $subject->resolve($action::class);

        $this->assertInstanceOf(Repository::class, $actual->repository);
    }

    #[Test]
    public function resolve_オプショナルな依存の解決()
    {
        $action = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            public DateTimeInterface $now;

            public function depends(string $testNow = '2010-11-12')
            {
                $this->now = new DateTimeImmutable($testNow);
            }
        };

        $container = new Container([
            $action::class => fn () => $action,
        ]);
        $subject = new ActionResolver($container);

        $actual = $subject->resolve($action::class);

        $this->assertSame('2010-11-12', $actual->now->format('Y-m-d'));
    }
}
