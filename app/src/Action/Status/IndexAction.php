<?php

namespace App\Action\Status;

use App\Repository\StatusRepository;
use O0h\KantanFw\Http\Action;

class IndexAction extends Action
{
    public function __construct(private readonly StatusRepository)
    {
        parent::__construct();
    }
    public function __invoke()
    {
        xdebug_break();
    }


}