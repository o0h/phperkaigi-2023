<?php

declare(strict_types=1);

namespace App\Action\Status;

use App\Repository\StatusRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Session;
use Psr\Http\Message\ResponseInterface;

class IndexAction extends Action
{
    private StatusRepository $repository;

    protected bool $needsAuth = true;

    public function depends(StatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(): ResponseInterface
    {
        $session = $this->helper->getSession();
        $user = $session->get('user');
        $statuses = $this->repository->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render([
            'statuses' => $statuses,
            'body' => '',
            '_token' => $this->helper->getCsrfToken(),
        ]);
    }
}
