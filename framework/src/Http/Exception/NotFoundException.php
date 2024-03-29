<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

class NotFoundException extends HttpException
{
    /**
     * @var int $code
     */
    protected $code = 404;
}
