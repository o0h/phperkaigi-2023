<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Middleware;

use O0h\KantanFw\Http\Exception\InvalidCsrfTokenException;
use O0h\KantanFw\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{
    /**
     * @phpstan-var array{sessionKey: string, postField: string, strength: positive-int, rememberLimit: 5} $config
     */
    private array $config = [
        'sessionKey' => 'csrfToken',
        'postField' => '_token',
        'strength' => 32,
        'rememberLimit' => 5,
    ];

    /**
     * @throws InvalidCsrfTokenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Session $session */
        $session = $request->getAttribute('session');
        if ($request->getMethod() === 'POST') {
            $this->checkCsrfToken($request, $session);
        }

        $token = $this->appendToken($session);
        $request = $request->withAttribute('csrfToken', $token);

        return $handler->handle($request);
    }

    private function appendToken(Session $session): string
    {
        $token = base64_encode(random_bytes($this->config['strength']));
        $tokens = $this->getSessionTokens($session);
        $tokens[] = $token;

        $tokens = array_slice($tokens, $this->config['rememberLimit'] * -1);

        $session->set($this->config['sessionKey'], $tokens);

        return $token;
    }

    private function checkCsrfToken(ServerRequestInterface $request, Session $session): void
    {
        $token = $request->getParsedBody()[$this->config['postField']] ?? null;
        if (!$token) {
            throw new InvalidCsrfTokenException();
        }
        $tokens = $this->getSessionTokens($session);
        $consumeToken = array_search($token, $tokens, true);
        if ($consumeToken === false) {
            throw new InvalidCsrfTokenException();
        }
        unset($tokens[$consumeToken]);

        $session->set($this->config['sessionKey'], array_values($tokens));
    }

    /**
     * @phpstan-return  string[]
     */
    private function getSessionTokens(Session $session): array
    {
        /** @var array<string> $tokens */
        $tokens = $session->get($this->config['sessionKey']) ?? [];

        return $tokens;
    }
}
