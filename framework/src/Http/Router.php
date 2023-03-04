<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http;

use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private array $routes;

    public function __construct(array $definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    public function compileRoutes($definitions)
    {
        $routs = [];

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (str_starts_with($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }
            $pattern = '/' . implode('/', $tokens);
            $routs[$pattern] = $params;
        }

        return $routs;
    }

    public function resolve(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        $path = $serverRequest->getUri()->getPath();
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        foreach ($this->routes as $pattern => $action) {
            if (preg_match('#^'. $pattern . '$#', $path, $matches)) {
                $matches = array_filter($matches, fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                $serverRequest = $serverRequest
                    ->withAttribute('action', $action)
                    ->withAttribute('args', $matches);
            }
        }

        return $serverRequest;
    }
}
