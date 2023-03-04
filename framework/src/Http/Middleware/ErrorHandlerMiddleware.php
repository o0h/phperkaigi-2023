<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\View\View;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private View $view
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->renderErrorPage($e);
        }
    }

    private function renderErrorPage(\Throwable $e): ResponseInterface
    {
        $code = $e->getCode();
        if (!file_exists("errors/{$code}.php")) {
            $code = 500;
            $templatePath = 'errors/500';
            $e = new \InvalidArgumentException(
                sprintf('Template file errors/"%d".php is missing.', $e->getCode()),
                500,
                $e,
            );
        }

        $content = $this->view->render("errors/{$code}", ['errorMessage' => $e->getMessage()]);

        return $this->responseFactory->createResponse()
            ->withStatus($code)
            ->withBody(
                $this->streamFactory->createStream($content)
            );
    }
}
