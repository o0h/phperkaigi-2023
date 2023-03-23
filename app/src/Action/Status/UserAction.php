<?php

declare(strict_types=1);

namespace App\Action\Status;

use App\Repository\FollowingRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Session;

class UserAction extends Action
{
    private StatusRepository $statusRepository;
    private UserRepository $userRepository;
    private FollowingRepository $followingRepository;

    public function depends(
        StatusRepository $statusRepository,
        UserRepository $userRepository,
        FollowingRepository $followingRepository
    ) {
        $this->statusRepository = $statusRepository;
        $this->userRepository = $userRepository;
        $this->followingRepository = $followingRepository;
    }

    public function __invoke(string $userName)
    {
        $user = $this->userRepository->fetchByUserName($userName);
        if (!$user) {
            throw new NotFoundException();
        }

        $statuses = $this->statusRepository->fetchAllByUserId($user['id']);

        $session = $this->helper->getSession();
        $following = null;
        if ($session->isAuthenticated()) {
            $my = $session->get('user');
            if ($my['id'] !== $user['id']) {
                $following = $this->followingRepository->isFollowing($my['id'], $user['id']);
            }
        }

        return $this->render([
            'user' => $user,
            'statuses' => $statuses,
            'following' => $following,
            '_token' => $this->helper->getCsrfToken(),
        ]);
    }
}
