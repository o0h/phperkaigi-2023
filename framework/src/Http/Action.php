<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http;

use O0h\KantanFw\View\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Action
{
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        readonly protected ServerRequestInterface $serverRequest,
        readonly protected ResponseFactoryInterface $responseFactory,
        protected View $view
    ) {
    }


    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function render()
    {

    }
}
