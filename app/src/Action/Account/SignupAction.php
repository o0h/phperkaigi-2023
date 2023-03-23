<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\RedirectException;

class SignupAction extends Action
{
    private UserRepository $repository;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        return $this->render([
            'userName' => '',
            'password' => '',
            '_token' => $this->helper->getCsrfToken(),
        ]);
    }
}
