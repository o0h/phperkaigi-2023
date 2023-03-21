<?php

namespace O0h\KantanFw\Http\Message;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{

    public function createStream(string $content = ''): StreamInterface
    {
        return new Stream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        throw new \LogicException(__METHOD__ . 'は実装されていません');
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}