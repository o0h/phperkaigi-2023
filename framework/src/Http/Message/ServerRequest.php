<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest implements ServerRequestInterface
{
    use RequestTrait;

    protected array $attributes = [];

    public function __construct(
        protected string $protocolVersion = '1.1',
        protected string $method = 'GET',
        protected UriInterface $uri = new Uri(),
        array $headers = [],
        protected StreamInterface $body = new Stream(''),
        private array $serverParams = [],
        private array $cookieParams = [],
        private array $queryParams = [],
        private $parsedBody = null,
        private array $uploadedFiles = [],
    ) {
        $this->headers = array_map(fn ($value) => (array)$value, $headers);
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): self
    {
        $new = clone $this;
        $new->cookieParams = $cookies;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query): array
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        throw new LogicException(__METHOD__ . 'は実装されていません');
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data): self
    {
        if (!(
            $data === null ||
            is_array($data) ||
            is_object($data)
        )) {
            throw new InvalidArgumentException('nullかarrayかobjectのみ利用できます');
        }

        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value): self
    {
        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name): self
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $this;
        }
        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }
}
