<?php

declare(strict_types=1);

namespace App\Action\Status;

use App\Repository\StatusRepository;
use O0h\KantanFw\Http\Action\Action;
use O0h\KantanFw\Http\Exception\NotFoundException;
use O0h\KantanFw\Http\Exception\RedirectException;
use Psr\Http\Message\ResponseInterface;

class PostAction extends Action
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
        if (!$this->helper->is('POST')) {
            throw new NotFoundException();
        }

        $body = $this->helper->getPostData('body');

        $errors = [];

        if (!strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } elseif (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $user = $session->get('user');
            $this->repository->insert($user['id'], $body);

            throw new RedirectException('/');
        }

        $user = $session->get('user');
        $statuses = $this->repository->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render([
            'errors' => $errors,
            'body' => $body,
            'statuses' => $statuses,
            '_token' => $this->helper->getCsrfToken(),
        ], 'Status/index');
    }
}
