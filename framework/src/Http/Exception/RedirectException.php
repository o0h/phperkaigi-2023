<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

class RedirectException extends HttpException
{
    public function __construct(public readonly string $redirectTo, public $code = 302)
    {
    }
}
