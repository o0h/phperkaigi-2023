<?php

return [
    '/' => \App\Action\Status\IndexAction::class,
    '/signin' => \App\Action\Account\SigninAction::class,
    '/user/:userName' => \App\Action\Status\UserAction::class,
    '/user/:userName/status/:id' => \App\Action\Status\ShowAction::class,
    '/status' => \App\Action\Status\IndexAction::class,
    '/status/post' => \App\Action\Status\PostAction::class,
    '/account' => \App\Action\Account\IndexAction::class,
    '/account/authenticate' => \App\Action\Account\AuthenticateAction::class,
    '/follow' => \App\Action\Account\FollowAction::class,
    '/account/signup' => \App\Action\Account\SignupAction::class,
    '/account/signout' => \App\Action\Account\SignoutAction::class,
    '/account/register' => \App\Action\Account\RegisterAction::class,
];
