<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http;

use Psr\Http\Message\ResponseInterface;

class Emitter
{
    /**
     * レスポンスオブジェクトから、標準出力への出力を行う
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function emit(ResponseInterface $response): void
    {
        header(
            header: sprintf('HTTP/1.1 %d %s', $response->getStatusCode(), $response->getReasonPhrase()),
            response_code: $response->getStatusCode(),
        );
        foreach ($response->getHeaders() as $header) {
            [$headerField, $headerValue] = $header;
            header($headerField . ': ' .$headerValue);
        }

        echo $response->getBody();
    }
}
