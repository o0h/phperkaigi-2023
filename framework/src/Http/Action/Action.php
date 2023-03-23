<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Action;

use O0h\KantanFw\Http\Exception\RedirectException;
use O0h\KantanFw\View\View;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class Action
{
    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;
    protected bool $needsAuth = false;
    protected const SIGNIN_ACTION = '/signin';

    protected ?RequestHelper $helper;

    final public function __construct(
        readonly protected ResponseFactoryInterface $responseFactory,
        readonly protected StreamFactoryInterface $streamFactory,
    ) {
        assert(defined('TEMPLATE_PATH'));
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
        $this->helper = new RequestHelper($request);
    }

    public function getRequest(): ServerRequestInterface
    {
        assert($this->request instanceof ServerRequestInterface);

        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        if ($this->response) {
            return $this->response;
        }
        $response = $this->responseFactory->createResponse();
        $this->setResponse($response);

        return $response;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getView(): View
    {
        $view = new View(TEMPLATE_PATH, [
            'request' => $this->getRequest(),
            'baseUrl' => $this->getRequest()->getAttribute('baseUrl'),
        ]);

        return $view;
    }

    public function filterAuth(): void
    {
        if (!$this->needsAuth) {
            return;
        }
        $session = $this->helper->getSession();
        if (!$session->isAuthenticated()) {
            throw new RedirectException(self::SIGNIN_ACTION);
        }
    }

    /**
     * @phpstan-param  array<string, mixed> $variables
     */
    public function render(array $variables, string $template = null, string|false $layout = 'default'): ResponseInterface
    {
        $view = $this->getView();

        if (is_null($template)) {
            $template = $this->getDefaultTemplate();
        }

        $content = $view->render($template, $variables, $layout);
        $stream = $this->streamFactory->createStream($content);

        return $this->getResponse()->withBody($stream);
    }

    private function getDefaultTemplate(): string
    {
        if (isset($this->template)) {
            return $this->template;
        }
        $action = get_class($this);
        $paths = array_slice(
            explode('\\', $action),
            array_search('Action', explode('\\', $action), true) + 1
        );
        $action = array_pop($paths);
        assert($action !== null && str_ends_with($action, 'Action'));
        $template = substr(lcfirst($action), 0, -6);

        return implode('/', [...$paths, $template]);
    }
}
