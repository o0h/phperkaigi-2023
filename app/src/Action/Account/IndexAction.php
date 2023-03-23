<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;

class IndexAction extends Action
{
    private UserRepository $repository;

    public function depends(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $session = $this->helper->getSession();
        $user = $session->get('user');
        $followings = $this->repository->fetchAllFollowingsByUserId($user['id']);

        return $this->render([
            'user' => $user,
            'followings' => $followings,
        ]);
    }
}
