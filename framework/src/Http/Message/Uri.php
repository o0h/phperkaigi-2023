<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private string $scheme;
    private string $host;
    private string $path;

    public function __construct(string $uri = '')
    {
        $uri = parse_url($uri);

        $this->scheme = $uri['scheme'] ?? 'https://';
        $this->host = $uri['host'] ?? 'localhost';
        $this->path = $uri['path'] ?? '';
    }


    public function getScheme()
    {
        // TODO: Implement getScheme() method.
    }

    public function getAuthority()
    {
        // TODO: Implement getAuthority() method.
    }

    public function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
    }

    public function getHost()
    {
        // TODO: Implement getHost() method.
    }

    public function getPort()
    {
        // TODO: Implement getPort() method.
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        // TODO: Implement getQuery() method.
    }

    public function getFragment()
    {
        // TODO: Implement getFragment() method.
    }

    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
    }

    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
