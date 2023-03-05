<?php

namespace O0h\KantanFw\Http\Exception;

class RedirectException extends HttpException
{
    public function __construct(public readonly string $redirectTo, public $code = 302)
    {
    }
}