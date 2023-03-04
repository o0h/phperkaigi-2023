<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

class HttpException extends \RuntimeException
{
    public $code = 500;
}
