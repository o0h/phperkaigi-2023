<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Router;

use O0h\KantanFw\Http\Action\Action;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * @var array<string, class-string<Action>> $routes
     */
    private array $routes;

    /**
     * @phpstan-param array<string, class-string<Action>> $definitions プレースホルダーを埋め込んだURLパスのテンプレートと対応するAction
     */
    public function __construct(array $definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    /**
     * @phpstan-param array<string, class-string<Action>> $definitions
     * @phpstan-return array<string, class-string<Action>> regexp表記のURLパスと対応するAction
     */
    public function compileRoutes(array $definitions): array
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

    /**
     * @phpstan-return array{'action': class-string<Action>, 'args': array<string, string>}|false
     */
    public function resolve(ServerRequestInterface $serverRequest): array|false
    {
        $path = $serverRequest->getUri()->getPath();
        // @codeCoverageIgnoreStart
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        // @codeCoverageIgnoreEnd

        foreach ($this->routes as $pattern => $action) {
            if (!preg_match('#^'. $pattern . '$#', $path, $matches)) {
                continue;
            }
            $matches = array_filter($matches, fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
            return [
                'action' => $action,
                'args' => $matches
            ];
        }

        return false;
    }
}
