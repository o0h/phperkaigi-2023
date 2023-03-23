<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Message;

use O0h\KantanFw\Http\Message\Exception\MissingResourceException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotReadableException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotSeekableException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotWritableException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    /** @var ?resource $resource */
    private $resource;

    /**
     * @param resource|string|null $contents
     */
    public function __construct($contents = null)
    {
        if ($contents === null) {
            $contents = tmpfile();
        }
        if (is_resource($contents)) {
            $this->resource = $contents;
        } else {
            $this->resource = $this->convertToResource($contents);
        }
    }


    public function __toString(): string
    {
        return $this->getContents();
    }

    public function close(): void
    {
        if (!$this->resource) {
            return;
        }

        fclose($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->close();
        $this->resource = null;

        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        if (!$this->resource) {
            return null;
        }

        $stats = fstat($this->resource);

        return $stats['size'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        if (!$this->resource) {
            throw new MissingResourceException();
        }
        $tell = ftell($this->resource);
        if (!is_int($tell)) {
            throw new RuntimeException();
        }

        return $tell;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return $this->resource && feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        return $this->resource && $this->getMetadata('seekable');
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): int
    {
        if (!$this->resource) {
            throw new MissingResourceException();
        }
        if (!$this->isSeekable()) {
            throw new ResourceNotSeekableException();
        }

        return fseek($this->resource, $offset, $whence);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        if (!$this->resource) {
            return false;
        }
        $mode = $this->getMetadata('mode');
        if (!$mode) {
            return false;
        }

        return preg_match('/[wacx+]/', $mode);
    }

    /**
     * @inheritDoc
     */
    public function write($string): int
    {
        if (!$this->isWritable()) {
            throw new ResourceNotWritableException();
        }

        $written = fwrite($this->resource, $string);
        if ($written === false) {
            throw new RuntimeException('Failed to write');
        }

        return $written;
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }
        $mode = $this->getMetadata('mode');

        return preg_match('/[r+]/', $mode) === 1;
    }

    /**
     * @inheritDoc
     */
    public function read($length): int|string
    {
        if (!$this->isReadable()) {
            throw new ResourceNotReadableException();
        }

        $read = fread($this->resource, $length);
        if ($read === false) {
            throw new RuntimeException('Failed to read');
        }

        return $read;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new ResourceNotReadableException();
        }

        $this->rewind();
        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            throw new RuntimeException('Failed to read');
        }

        return $contents;
    }

    /**
     * {@inheritDoc}
     *
     * @phpstan-return {$key is string ? int|string|null : array}
     */
    public function getMetadata($key = null): mixed
    {
        if (!$this->resource) {
            return null;
        }
        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    /**
     * コンテンツ(文字列)をリソースに格納して返す
     *
     * @param string $contents
     * @return resource
     */
    private function convertToResource(string $contents)
    {
        $resource = tmpfile();
        $written = fwrite($resource, $contents);
        if ($written === false) {
            throw new RuntimeException('Failed to write contents');
        }

        return $resource;
    }
}
