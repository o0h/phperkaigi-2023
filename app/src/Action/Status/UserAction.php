<?php

declare(strict_types=1);

namespace App\Action\Status;

use O0h\KantanFw\Http\Action;
use O0h\KantanFw\Http\Emitter;

class UserAction extends Action
{

    public function __invoke(string $userName)
    {
        xdebug_break();
    }
}
