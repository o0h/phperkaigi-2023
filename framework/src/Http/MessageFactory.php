<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

use const UPLOAD_ERR_OK;

class MessageFactory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        // TODO: Implement createRequest() method.
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implement createResponse() method.
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        // TODO: Implement createServerRequest() method.
    }

    public function createStream(string $content = ''): StreamInterface
    {
        // TODO: Implement createStream() method.
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        // TODO: Implement createStreamFromFile() method.
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        // TODO: Implement createStreamFromResource() method.
    }

    public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface
    {
        // TODO: Implement createUploadedFile() method.
    }

    public function createUri(string $uri = ''): UriInterface
    {
        // TODO: Implement createUri() method.
    }
}
