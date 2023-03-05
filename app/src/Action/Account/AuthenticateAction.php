<?php

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
        $request = $this->getRequest();
        $session = $request->getAttribute('session');
        if ($session->isAuthenticated()) {
            throw new RedirectException('/account');
        }

        xdebug_break();
        if ($request->getMethod() !== 'POST') {
            throw new NotFoundException();
        }

        $token = $request->getParsedBody()['_token'] ?? '';
        if (!$this->checkCsrfToken('account/signin', $token)) {
            throw new RedirectException('/signin');
        }

        $userName = $request->getParsedBody()['user_name'] ?? '';
        $password = $request->getParsedBody()['password'] ?? '';

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
            // '_token' => $this->generateCsrfToken('account/signin'),
            '_token' => 'dummy-token',
        ], 'signin');

    }

}