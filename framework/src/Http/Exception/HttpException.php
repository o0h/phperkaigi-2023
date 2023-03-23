<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

use RuntimeException;

class HttpException extends RuntimeException
{
    /**
     * @var int $code
     */
    protected $code = 500;
}
