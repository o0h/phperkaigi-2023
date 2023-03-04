<?php

return [
    '/' => \App\Action\HomeAction::class,
    '/:userName' => \App\Action\Status\UserAction::class,
];
