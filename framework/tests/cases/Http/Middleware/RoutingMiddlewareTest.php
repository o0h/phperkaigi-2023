<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\Http\Middleware;

use O0h\KantanFw\DI\Container;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Message\ServerRequest;
use O0h\KantanFw\Http\Message\Uri;
use O0h\KantanFw\Http\Middleware\RoutingMiddleware;
use O0h\KantanFw\Http\ResponseFactory;
use O0h\KantanFw\Http\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddlewareTest extends TestCase
{
    public function testProcess()
    {
        $responseMock = $this->createMock(ResponseInterface::class);
//        $actionMock = $this->getMockBuilder(Action::class)
//            ->disableOriginalConstructor()
//            ->addMethods(['__invoke'])
//            ->getMock();
//        $actionMock->expects($this->once())
//            ->method('__invoke')
//            ->with()
//            ->willReturn($responseMock);

        $action = new class() extends Action
        {

        }
        $router = new Router([
            '/dont-anything/:id' => $actionMock::class,
        ]);
        $responseFactory = new ResponseFactory();
        $dependencies = [
            $actionMock::class => fn () => $actionMock,
        ];
        $container = new Container($dependencies);

        $subject = new RoutingMiddleware($router, $responseFactory, $container);

        $request = new ServerRequest(uri: new Uri('/dont-anything/lucky'));
        $handler = $this->createMock(RequestHandlerInterface::class);

        $actual = $subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $actual);
    }
}
