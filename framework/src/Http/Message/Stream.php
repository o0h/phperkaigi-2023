<?php

namespace O0h\KantanFw\Http\Message;

use O0h\KantanFw\Http\Message\Exception\MissingResourceException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotReadableException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotSeekableException;
use O0h\KantanFw\Http\Message\Exception\ResourceNotWritableException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /** @var ?resource $resource */
    private $resource;

    /**
     * @param resource|string $contents
     */
    public function __construct($contents)
    {
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

    public function getSize(): ?int
    {
        if (!$this->resource) {
            return null;
        }

        $stats = fstat($this->resource);

        return $stats['size'] ?? null;
    }

    public function tell(): int
    {
        if (!$this->resource) {
            throw new MissingResourceException();
        }
        $tell = ftell($this->resource);
        if (!is_int($tell)) {
            throw new \RuntimeException();
        }

        return $tell;
    }

    public function eof(): bool
    {
        return $this->resource && feof($this->resource);
    }

    public function isSeekable(): bool
    {
        return $this->resource && $this->getMetadata('seekable');
    }

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

    public function rewind(): void
    {
        $this->seek(0);
    }

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

    public function write($string): int
    {
        if (!$this->isWritable()) {
            throw new ResourceNotWritableException();
        }

        $written = fwrite($this->resource, $string);
        if ($written === false) {
            throw new \RuntimeException('Failed to write');
        }

        return $written;
    }

    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }
        $mode = $this->getMetadata('mode');

        return preg_match('/[r+]/', $mode);
    }

    public function read($length): int
    {
        if (!$this->isReadable()) {
            throw new ResourceNotReadableException();
        }

        $read = fread($this->resource, $length);
        if ($read === false) {
            throw new \RuntimeException('Failed to read');
        }

        return $read;
    }

    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new ResourceNotReadableException();
        }

        $this->rewind();
        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            throw new \RuntimeException('Failed to read');
        }

        return $contents;
    }

    /**
     * {@inheritDoc}
     *
     * @phpstan-return {$key is string ? int|string|null : array}
     * @param ?string $key
     * @return mixed
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
            throw new \RuntimeException('Failed to write contents');
        }

        return $resource;

    }
}
