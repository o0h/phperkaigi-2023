<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest(
            method: $method,
            uri: (new UriFactory())->createUri($uri),
            serverParams: $serverParams,
        );
    }

    /**
     * スーパーグローバル変数からServerRequestインスタンスを組み立てて返す
     *
     * @return ServerRequest
     */
    public static function fromGlobals(): ServerRequest
    {
        return new ServerRequest(
            method: $_SERVER['REQUEST_METHOD'],
            uri: (new UriFactory())->createUri($_SERVER['REQUEST_URI']),
            serverParams: $_SERVER,
            cookieParams: $_COOKIE,
            queryParams: $_GET,
            parsedBody: $_POST,
        );
    }
}
