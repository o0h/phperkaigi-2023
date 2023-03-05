<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Action;

use O0h\KantanFw\Database\Manager;
use O0h\KantanFw\View\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

abstract class Action
{
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        readonly protected ServerRequestInterface $serverRequest,
        readonly protected ResponseFactoryInterface $responseFactory,
        readonly protected StreamFactoryInterface $streamFactory,
        readonly protected Manager $dbManager,
    ) {
    }


    public function getRequest(): ServerRequestInterface
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

    public function getView(): View
    {
        $view = new View(TEMPLATE_PATH, [
            'request' => $this->getRequest(),
            'baseUrl' => $this->request->getAttribute('baseUrl'),
        ]);

        return $view;
    }

    protected function checkCsrfToken(string $formName, string $token): bool
    {
        xdebug_break();
        return true;

    }

    public function render(array $variables, string $template = null, string|false $layout = 'default'): ResponseInterface
    {
        $view = $this->getView();

        if (is_null($template)) {
            $template = $this->getDefaultTemplate();
        }

        $content = $view->render($template, $variables, $layout);

        $stream =  $this->streamFactory->createStream($content);

        return $this->responseFactory->createResponse()
            ->withBody($stream);
    }

    private function getDefaultTemplate(): string
    {
        if (isset($this->template)) {
            return $this->template;
        }
        $action = get_class($this);
        $paths = array_slice(explode('\\', $action), array_search('Action', explode('\\', $action)) + 1);
        $action = array_pop($paths);
        $template = substr(lcfirst($action), 0,  -6);

        return implode('/', [...$paths, $template]);
    }
}
