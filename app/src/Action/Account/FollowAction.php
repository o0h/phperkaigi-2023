<?php

declare(strict_types=1);

namespace App\Action\Account;

use App\Repository\FollowingRepository;
use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Exception\RedirectException;

class FollowAction extends Action
{
    private UserRepository $userRepository;
    private FollowingRepository $followingRepository;

    protected bool $needsAuth = true;

    public function depends(UserRepository $userRepository, FollowingRepository $followingRepository)
    {
        $this->userRepository = $userRepository;
        $this->followingRepository = $followingRepository;
    }

    public function __invoke()
    {
        if (!$this->helper->is('POST')) {
            throw new NotFoundException();
        }

        $followingName = $this->helper->getPostData('following_name');
        if (!$followingName) {
            throw new NotFoundException();
        }

        $followUser = $this->userRepository->fetchByUserName($followingName);
        if (!$followUser) {
            throw new NotFoundException();
        }

        $session = $this->helper->getSession();
        $user = $session->get('user');

        if ($user['id'] !== $followUser['id']
            && !$this->followingRepository->isFollowing($user['id'], $followUser['id'])
        ) {
            $this->followingRepository->insert($user['id'], $followUser['id']);
        }

        throw new RedirectException('/account');
    }
}
