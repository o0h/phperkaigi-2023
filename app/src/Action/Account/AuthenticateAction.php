<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Exception\RedirectException;

class AuthenticateAction extends Action
{
    private UserRepository $repository;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $session = $this->helper->getSession();
        if ($session->isAuthenticated()) {
            throw new RedirectException('/account');
        }

        if (!$this->helper->is('POST')) {
            throw new NotFoundException();
        }

        $userName = $this->helper->getPostData('user_name');
        $password = $this->helper->getPostData('password');

        $errors = [];

        if (!strlen($userName)) {
            $errors[] = 'ユーザIDを入力してください';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {
            $user = $this->repository->fetchByUserName($userName);

            if (!$user ||
                $user['password'] !== $this->repository->hashPassword($password)
            ) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $session->setAuthenticated(true);
                $session->set('user', $user);

                throw new RedirectException('/');
            }
        }

        return $this->render([
            'userName' => $userName,
            'password' => $password,
            'errors' => $errors,
            '_token' => $this->helper->getCsrfToken(),
        ], 'Account/signin');
    }
}
