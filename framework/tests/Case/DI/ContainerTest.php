<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\DI;

use DateTimeImmutable;
use O0h\KantanFw\DI\Container;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use stdClass;

class ContainerTest extends TestCase
{
    public function testGetWithFactory(): void
    {
        $configuration = [
            ServerRequestFactoryInterface::class =>
                fn () => $this->createMock(ServerRequestFactoryInterface::class),
        ];
        $subject = new Container($configuration);
        $actual = $subject->get(ServerRequestFactoryInterface::class);
        $this->assertInstanceOf(ServerRequestFactoryInterface::class, $actual);
    }

    public function testGetWithFactoryInstantiateInEveryTime(): void
    {
        $configuration = [
            'some' => fn () => new stdClass(),
        ];
        $subject = new Container($configuration);

        $actual1 = $subject->get('some');
        $actual2 = $subject->get('some');

        $this->assertNotSame($actual1, $actual2);
    }

    public function testGetRecursive()
    {
        $configuration = [
            ServerRequestFactoryInterface::class =>
                fn () => $this->createMock(ServerRequestFactoryInterface::class),
            'SomeComposition' => function (Container $container) {
                $some = $container->get(ServerRequestFactoryInterface::class);
                return new class ($some) {
                    public function __construct(public $some)
                    {
                    }
                };
            },
        ];

        $subject = new Container($configuration);
        $actual = $subject->get('SomeComposition');
        $this->assertInstanceOf(ServerRequestFactoryInterface::class, $actual->some);
    }

    public function testGetSingleton(): void
    {
        $configuration = [
            'now|singleton' => fn () => new DateTimeImmutable(),
        ];
        $subject = new Container($configuration);

        $actual1 = $subject->get('now');
        $actual2 = $subject->get('now');

        $this->assertSame($actual1, $actual2);
    }

    public function testHas()
    {
        $configuration = [
            'now|singleton' => fn () => new DateTimeImmutable(),
        ];
        $subject = new Container($configuration);

        $this->assertTrue($subject->has('now'));
        $this->assertFalse($subject->has('not-now'));
    }
}
