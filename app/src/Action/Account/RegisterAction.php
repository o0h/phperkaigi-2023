<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Exception\RedirectException;

class RegisterAction extends Action
{
    private UserRepository $repository;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $request = $this->getRequest();
        if (!$this->helper->is('POST')) {
            throw new NotFoundException();
        }

        $userName = $this->helper->getPostData('user_name');
        $password = $this->helper->getPostData('password');

        $errors = [];

        if (!strlen($userName)) {
            $errors[] = 'ユーザIDを入力してください';
        } elseif (!preg_match('/^\w{3,20}$/', $userName)) {
            $errors[] = 'ユーザIDは半角英数字及びアンダースコアを3〜20文字以内で入力してください';
        } elseif (!$this->repository->isUniqueUserName($userName)) {
            $errors[] = 'ユーザIDはすでに使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        } elseif (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4〜30文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $session = $this->helper->getSession();
            $this->repository->insert($userName, $password);
            $session->setAuthenticated(true);

            $user = $this->repository->fetchByUserName($userName);
            $session->set('user', $user);

            throw new RedirectException('/');
        }

        return $this->render([
            'userName' => $userName,
            'password' => $password,
            'errors' => $errors,
            '_token' => $this->helper->getCsrfToken(),
        ], 'signup');
    }
}
