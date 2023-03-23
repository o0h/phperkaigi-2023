<?php

class AccountController extends Controller
{
    protected $authActions = ['index', 'signout'];

    public function signupAction()
    {
        return $this->render([
            'userName' => '',
            'password' => '',
            '_token' => $this->generateCsrfToken('account/signup'),
        ]);
    }

    public function registerAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('/account/signup');
        }

        $userName = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = [];

        if (!strlen($userName)) {
            $errors[] = 'ユーザIDを入力してください';
        } elseif (!preg_match('/^\w{3,20}$/', $userName)) {
            $errors[] = 'ユーザIDは半角英数字及びアンダースコアを3〜20文字以内で入力してください';
        } elseif (!$this->dbManager->get('User')->isUniqueUserName($userName)) {
            $errors[] = 'ユーザIDはすでに使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        } elseif (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4〜30文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $this->dbManager->get('User')->insert($userName, $password);
            $this->session->setAuthenticated(true);

            $user = $this->dbManager->get('User')->fetchByUserName($userName);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        return $this->render([
            'userName' => $userName,
            'password' => $password,
            'errors' => $errors,
            '_token' => $this->generateCsrfToken('account/signup'),
        ], 'signup');
    }

    public function indexAction()
    {
        $user = $this->session->get('user');
        $followings = $this->dbManager->get('User')
            ->fetchAllFollowingsByUserId($user['id']);

        return $this->render([
            'user' => $user,
            'followings' => $followings,
        ]);
    }

    public function signinAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        return $this->render([
            'userName' => '',
            'password' => '',
            '_token' => $this->generateCsrfToken('account/signin'),
        ]);
    }

    public function authenticateAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('account/signin');
        }

        $userName = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = [];

        if (!strlen($userName)) {
            $errors[] = 'ユーザIDを入力してください';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {
            $userRepository = $this->dbManager->get('User');
            $user = $userRepository->fetchByUserName($userName);

            if (!$user ||
                $user['password'] !== $userRepository->hashPassword($password)
            ) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $this->session->setAuthenticated(true);
                $this->session->set('user', $user);

                return $this->redirect('/');
            }
        }

        return $this->render([
            'userName' => $userName,
            'password' => $password,
            'errors' => $errors,
            '_token' => $this->generateCsrfToken('account/signin'),
        ], 'signin');
    }

    public function signoutAction()
    {
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');

    }

    public function followAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $followingName = $this->request->getPost('following_name');
        if (!$followingName) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/user/' . $followingName);
        }

        $followUser = $this->dbManager->get('User')
            ->fetchByUserName($followingName);
        if (!$followUser) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $followingRepository = $this->dbManager->get('Following');
        if ($user['id'] !== $followUser['id']
            && !$followingRepository->isFollowing($user['id'], $followUser['id'])
        ) {
            $followingRepository->insert($user['id'], $followUser['id']);
        }

        return $this->redirect('/account');
    }
}