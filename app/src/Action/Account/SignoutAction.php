<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\RedirectException;

class SignoutAction extends Action
{
    private UserRepository $repository;

    protected bool $needsAuth = true;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $session = $this->getRequest()->getAttribute('session');
        $session->clear();
        $session->setAuthenticated(false);

        throw new RedirectException('/signin');
    }
}
