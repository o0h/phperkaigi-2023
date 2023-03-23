<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Case\Http\Middleware;

use Generator;
use O0h\KantanFw\DI\Container;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Exception\RedirectException;
use O0h\KantanFw\Http\Message\ResponseFactory;
use O0h\KantanFw\Http\Message\ServerRequest;
use O0h\KantanFw\Http\Message\StreamFactory;
use O0h\KantanFw\Http\Message\Uri;
use O0h\KantanFw\Http\Middleware\RoutingMiddleware;
use O0h\KantanFw\Http\Router\ActionResolver;
use O0h\KantanFw\Http\Router\Router;
use O0h\KantanFw\Http\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(RoutingMiddleware::class)]
class RoutingMiddlewareTest extends TestCase
{
    #[DataProvider('routingProvider')]
    public function testProcess(string $expectedContent, string $requestPath): void
    {
        $subject = $this->createSubject();

        $request = new ServerRequest(uri: new Uri($requestPath));
        $handler = $this->createMock(RequestHandlerInterface::class);

        $actual = $subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $actual);
        $this->assertSame($expectedContent, $actual->getBody()->getContents());
    }

    public static function routingProvider(): Generator
    {
        yield 'パラメータなしのルーティング' => ['action1 is called', '/dont-anything'];
        yield 'パラメータありのルーティング' => ['action2 is called with taro-san', '/do/for/taro-san'];
    }

    public function testProcessNeedsSignin(): void
    {
        $subject = $this->createSubject();

        $sessionStub = $this->createStub(Session::class);
        $sessionStub->method('isAuthenticated')
            ->willReturn(true);
        $request = (new ServerRequest(uri: new Uri('/private/action')))
            ->withAttribute('session', $sessionStub);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $actual = $subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $actual);
    }

    public function testProcessNeedsSigninToRedirect(): void
    {
        $subject = $this->createSubject();

        $sessionStub = $this->createStub(Session::class);
        $sessionStub->method('isAuthenticated')
            ->willReturn(false);
        $request = (new ServerRequest(uri: new Uri('/private/action')))
            ->withAttribute('session', $sessionStub);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /signin');

        $subject->process($request, $handler);
    }

    public function testProcessToNotFound(): void
    {
        $subject = $this->createSubject();

        $request = new ServerRequest(uri: new Uri('/invalid-path'));
        $handler = $this->createMock(RequestHandlerInterface::class);

        $this->expectException(NotFoundException::class);
        $subject->process($request, $handler);
    }

    private static function createSubject(): RoutingMiddleware
    {
        $action1 = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            public function __invoke()
            {
                return $this->responseFactory
                    ->createResponse(200)
                    ->withBody(
                        $this->streamFactory->createStream('action1 is called')
                    );
            }
        };
        $action2 = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            public function __invoke(string $id)
            {
                return $this->responseFactory
                    ->createResponse(200)
                    ->withBody(
                        $this->streamFactory->createStream("action2 is called with {$id}")
                    );
            }
        };
        $action3 = new class (new ResponseFactory(), new StreamFactory()) extends Action {
            protected bool $needsAuth = true;

            public function __invoke()
            {
                return $this->responseFactory->createResponse();
            }
        };

        $router = new Router([
            '/dont-anything' => $action1::class,
            '/do/for/:id' => $action2::class,
            '/private/action' => $action3::class,
        ]);

        $container = new Container([
            $action1::class => fn () => $action1,
            $action2::class => fn () => $action2,
            $action3::class => fn () => $action3,
        ]);
        $actionResolver = new ActionResolver($container);

        return new RoutingMiddleware($router, new ResponseFactory(), $actionResolver);
    }
}
