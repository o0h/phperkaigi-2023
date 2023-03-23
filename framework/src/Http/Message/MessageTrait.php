<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @see MessageInterface
 */
trait MessageTrait
{
    /** @var array[] */
    private array $headers = [];

    /**
     * @see MessageInterface::getProtocolVersion()
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @see MessageInterface::withProtocolVersion()
     */
    public function withProtocolVersion($version): self
    {
        $new = clone $this;
        $new->protocolVersion = $version;

        return $new;
    }

    /**
     * @see MessageInterface::getHeaders()
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @see MessageInterface::hasHeader()
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * @see MessageInterface::getHeader()
     */
    public function getHeader($name): array
    {
        $value = $this->headers[$name] ?? null;
        return  (array)$value;
    }

    /**
     * @see MessageInterface::getHeaderLine()
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @see MessageInterface::withHeader()
     */
    public function withHeader($name, $value): self
    {
        $new = clone $this;
        $new->headers[$name] = (array)$value;

        return $new;
    }

    /**
     * @see MessageInterface::withAddedHeader()
     */
    public function withAddedHeader($name, $value): self
    {
        $values = $this->getHeader($name);
        $values[] = $value;

        return $this->withHeader($name, $values);
    }

    /**
     * @see MessageInterface::withoutHeader()
     */
    public function withoutHeader($name): self
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $new = clone $this;
        usnet($new->headers[$name]);

        return $new;
    }

    /**
     * @see MessageInterface::getBody()
     */
    public function getBody(): Stream
    {
        return $this->body;
    }

    /**
     * @see MessageInterface::withBody()
     */
    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }
}
