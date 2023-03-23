<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

class MissingTemplateException extends HttpException
{
    /**
     * @var int $code
     */
    protected $code = 500;
}
