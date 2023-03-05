<?php

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\RedirectException;

class SigninAction extends Action
{
    private UserRepository $repository;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $session = $this->getRequest()->getAttribute('session');
        if ($session->isAuthenticated()) {
            throw new RedirectException('/account');
        }

        return $this->render([
            'userName' => '',
            'password' => '',
            // '_token' => $this->generateCsrfToken('account/signin'),
            '_token' => 'dummyyyy',
        ]);
    }


}