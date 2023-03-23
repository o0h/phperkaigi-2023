<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use function preg_match;

/**
 * @see RequestInterface
 */
trait RequestTrait
{
    use MessageTrait;

    protected ?string $requestTarget = null;

    /**
     * @see RequestInterface::getRequestTarget()
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        $query = $this->uri->getQuery();
        if ($query) {
            $target .= '?' . $query;
        }

        return $target ?: '/';

    }

    /**
     * @param string $requestTarget
     *
     * @see RequestInterface::withRequestTarget()
     */
    public function withRequestTarget($requestTarget): self
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }
        $new = clone $this;
        $new->requestTarget = $requestTarget;

        return $new;
    }

    /**
     * @see RequestInterface::getMethod()
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @see RequestInterface::withMethod()
     */
    public function withMethod($method): self
    {
        $new = clone $this;
        $new->method = $method;

        return $new;
    }

    /**
     * @see RequestInterface::getUri()
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @see RequestInterface::withUri()
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost && $uri->getHost()) {
            $host = $uri->getHost();
            $new->headers['Host'] = $host;
        }

        return $new;
    }
}
