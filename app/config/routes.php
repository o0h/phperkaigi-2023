<?php

return [
    '/' => \App\Action\Status\IndexAction::class,
    '/:userName' => \App\Action\Status\UserAction::class,
];
