<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http {
    use O0h\KantanFw\Test\Asset\HeaderStack;

    function header(string $header, bool $replace = true, int $response_code = 0): void
    {
        HeaderStack::push($header);
    }
}
