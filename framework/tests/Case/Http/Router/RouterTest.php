<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\Http\Router;

use Generator;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Message\ResponseFactory;
use O0h\KantanFw\Http\Message\ServerRequest;
use O0h\KantanFw\Http\Message\StreamFactory;
use O0h\KantanFw\Http\Message\Uri;
use O0h\KantanFw\Http\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @dataProvider definitionProvider
     *
     * @param array<string, class-string<Action>> $expects
     * @param array<string, class-string<Action>> $definitions
     *
     */
    public function testCompileRoutes(array $expects, array $definitions): void
    {
        $subject = new Router($definitions);

        $actual = $subject->compileRoutes($definitions);

        $this->assertEquals($expects, $actual);
    }

    /**
     * @return Generator [期待する値, Routingの定義]のタプル
     */
    public static function definitionProvider(): Generator
    {
        $action1 = self::getDummyAction();
        $definitions = [
            '/' => $action1::class,
        ];
        $expects = [
            '/' => $action1::class,
        ];
        yield 'パラメータなしのルーティング/' => [$expects, $definitions];

        $definitions = [
            '/action/:param1/:param2' => $action1::class,
        ];
        $expects = [
            '/action/(?P<param1>[^/]+)/(?P<param2>[^/]+)' => $action1::class,
        ];

        yield 'パラメータありルーティング' => [$expects, $definitions];

        $action2 = self::getDummyAction();
        $action3 = self::getDummyAction();

        $definitions = [
            '/' => $action1::class,
            '/pass' => $action2::class,
            '/pass/:param' => $action3::class,
        ];
        $expects = [
            '/' => $action1::class,
            '/pass' => $action2::class,
            '/pass/(?P<param>[^/]+)' => $action3::class,
        ];
        yield '複数のルーティング' => [$expects, $definitions];
    }

    public function testResolve(): void
    {
        $action = self::getDummyAction();
        $definitions = [
            '/invoke/:name/:sub' => $action::class,
        ];
        $subject = new Router($definitions);

        $uri = new Uri('https://example.com/invoke/some/action');
        $request = new ServerRequest(uri: $uri);
        $actual = $subject->resolve($request);
        assert($actual !== false);

        $this->assertSame($action::class, $actual['action']);
        $this->assertSame(['name' => 'some', 'sub' => 'action'], $actual['args']);
    }

    public function testResolveNotMatch(): void
    {
        $action = self::getDummyAction();
        $definitions = [
            '/' => $action::class,
        ];
        $subject = new Router($definitions);

        $uri = new Uri('https://example.com/invoke/some/action');
        $request = new ServerRequest(uri: $uri);
        $actual = $subject->resolve($request);

        $this->assertFalse($actual);
    }

    private static function getDummyAction(): Action
    {
        return new class (new ResponseFactory(), new StreamFactory()) extends Action {
        };
    }
}
