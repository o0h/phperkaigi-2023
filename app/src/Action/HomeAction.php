<?php

declare(strict_types=1);

namespace App\Action;

use O0h\KantanFw\Http\Action;
use Psr\Http\Message\ResponseInterface;

class HomeAction extends Action
{
    public function __invoke(): ResponseInterface
    {
        xdebug_break();
        $response = $this->getResponse();

        return $response;
    }
}
