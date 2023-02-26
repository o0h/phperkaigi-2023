<?php

class MiniBlogApplication extends Application
{
    protected $loginAction = ['account', 'signin'];

    public function getRootDir()
    {
        return __DIR__;
    }

    protected function registerRoutes()
    {
        return [
            '/' => ['controller' => 'status', 'action' => 'index'],
            '/status/post' => ['controller' => 'status', 'action' => 'post'],
            '/user/:user_name' => ['controller' => 'status', 'action' => 'user'],
            '/user/:user_name/status/:id' => ['controller' => 'status', 'action' => 'show'],
            '/account' => ['controller' => 'account', 'action' => 'index'],
            '/account/:action' => ['controller' => 'account'],
            '/follow' => ['controller' => 'account', 'action' => 'follow'],
        ];
    }

    protected function configure()
    {
        $this->dbManager->connect('master', [
            'dsn' => getenv('DATABASE_DSN'),
            'user' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PASSWORD'),
        ]);
    }
}