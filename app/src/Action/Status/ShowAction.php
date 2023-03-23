<?php

declare(strict_types=1);

namespace App\Action\Status;

use App\Repository\StatusRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;

class ShowAction extends Action
{
    private StatusRepository $repository;

    public function depends(StatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $userName, string $id)
    {
        $status = $this->repository->fetchByIdAndUserName($id, $userName);

        if (!$status) {
            throw new NotFoundException();
        }

        return $this->render(['status' => $status]);
    }
}
