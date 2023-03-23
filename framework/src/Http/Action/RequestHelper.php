<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Action;

use LogicException;
use O0h\KantanFw\Http\Session;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @phpstan-type method_type 'HEAD'|'GET'|'POST'|'PUT'|'PATCH'|'DELETE'|'PURGE'|'OPTIONS'|'TRACE'|'CONNECT'
 */
class RequestHelper
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    /**
     * @phpstan-param method_type $expect
     */
    public function is(string $expect): bool
    {
        $method = $this->request->getMethod();

        return $method === $expect;
    }

    public function getPostData(string $field): mixed
    {
        $body = $this->request->getParsedBody();

        return $body[$field] ?? null;
    }

    /**
     * @throws LogicException
     */
    public function getSession(): Session
    {
        /** @var Session|null $session */
        $session = $this->request->getAttribute('session');
        if (!$session) {
            throw new LogicException('事前にSessionをセットしてください');
        }

        return $session;
    }

    public function getCsrfToken(): string
    {
        /** @var string|null $token */
        $token = $this->request->getAttribute('csrfToken');
        if (!$token) {
            throw new LogicException('事前にCSRF Tokenをセットしてください');
        }

        return $token;
    }
}
