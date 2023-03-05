<?php

return [
    '/' => \App\Action\Status\IndexAction::class,
    '/signin' => \App\Action\Account\SigninAction::class,
    '/user/:userName' => \App\Action\Status\UserAction::class,
    '/status/post' => \App\Action\Status\PostAction::class,
    '/account/authenticate' => \App\Action\Status\IndexAction::class,
    '/account/authenticate' => \App\Action\Account\AuthenticateAction::class,
];
