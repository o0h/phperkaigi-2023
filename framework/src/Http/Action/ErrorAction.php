<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Action;

use O0h\KantanFw\Http\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorAction extends Action
{
    private Throwable $exception;

    public function setException(Throwable $exception): void
    {
        $this->exception = $exception;
    }

    public function __invoke(): ResponseInterface
    {
        $exception = $this->exception;
        $code = ($exception instanceof HttpException && $exception->getCode()) ? $exception->getCode() : 500;
        $template = "Error/{$code}";

        $response = $this->render(['errorMessage' => $exception->getMessage()], $template)
            ->withStatus($code);

        return $response;
    }
}
