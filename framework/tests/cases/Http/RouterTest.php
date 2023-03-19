<?php
declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\Http;

use O0h\KantanFw\Database\Manager;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Message\ServerRequest;
use O0h\KantanFw\Http\Message\Uri;
use O0h\KantanFw\Http\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

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
     * @return \Generator [期待する値, Routingの定義]のタプル
     */
    public function definitionProvider(): \Generator
    {
        $action1 = $this->getDummyAction();
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

        $action2 = $this->getDummyAction();
        $action3 = $this->getDummyAction();

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

    public function testResolve()
    {
        $action = $this->getDummyAction();
        $definitions = [
            '/invoke/:name/:sub' => $action::class,
        ];
        $subject = new Router($definitions);

        $uri = new Uri('https://example.com/invoke/some/action');
        $request = new ServerRequest(uri: $uri);
        $actual = $subject->resolve($request);

        $this->assertSame($action::class, $actual->getAttribute('action'));
        $this->assertSame(['name' => 'some', 'sub' => 'action'], $actual->getAttribute('args'));
    }

    public function testResolveNotMatch(): void
    {
        $action = $this->getDummyAction();
        $definitions = [
            '/' => $action::class,
        ];
        $subject = new Router($definitions);

        $uri = new Uri('https://example.com/invoke/some/action');
        $request = new ServerRequest(uri: $uri);
        $actual = $subject->resolve($request);

        $this->assertArrayNotHasKey('action', $actual->getAttributes());
        $this->assertArrayNotHasKey('args', $actual->getAttributes());
    }

    private function getDummyAction(): Action
    {
        $constructArgs = [
            'serverRequest' => $this->createMock(ServerRequestInterface::class),
            'responseFactory' => $this->createMock(ResponseFactoryInterface::class),
            'streamFactory' => $this->createMock(StreamFactoryInterface::class),
            'dbManager' => $this->createMock(Manager::class),
        ];

        return new class(...$constructArgs) extends Action {
        };
    }

}
