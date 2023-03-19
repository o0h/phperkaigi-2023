<?php

declare(strict_types=1);

namespace App\Action\Status;

use App\Repository\StatusRepository;
use O0h\KantanFw\Http\Action\Action;

class IndexAction extends Action
{
    private StatusRepository $repository;

    public function depends(StatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        // $user = $this->session->get('user');
        // $statuses = $this->dbManager->get('Status')
        //     ->fetchAllPersonalArchivesByUserId($user['id']);

        $statuses = $this->repository->fetchAllPersonalArchivesByUserId(1);

        return $this->render([
            'statuses' => $statuses,
            'body' => '',
            // FIXME: これはミドルウェアにやらせるかな
            // '_token' => $this->generateCsrfToken('status/post'),
            '_token' => 'tmp/dummy---',
        ]);
    }
}
