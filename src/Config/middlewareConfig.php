<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\Hello;
use App\Middlewares\JsonResponse;

return [

    'middleware' => [
        JsonResponse::class,
        AuthMiddleware::class,
    ],

    'middlewareAliases' => [
        'print' => Hello::class,
    ],
    
];