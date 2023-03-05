<?php

namespace O0h\KantanFw\Http\Action;
class ErrorAction extends Action
{
    private \Throwable $exception;

    public function setException(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function __invoke(): \Psr\Http\Message\ResponseInterface
    {
        $exception = $this->exception;
        $code = $exception->getCode() ?: 500;
        $template = "Error/{$code}";

        $resopnse = $this->render(['errorMessage' => $exception->getMessage()], $template)
            ->withStatus($code);

        return $resopnse;
    }
}