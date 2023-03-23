<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http\Exception;

class RedirectException extends HttpException
{
    /**
     * @param string $redirectTo
     * @param int $code
     */
    public function __construct(public readonly string $redirectTo, protected $code = 302)
    {
        $this->message = "Redirect to {$this->redirectTo}";
    }
}
