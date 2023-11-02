<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\Hello;
use App\Middlewares\HelloMiddleware;
use App\Middlewares\JsonResponse;

return [

    'middleware' => [
        JsonResponse::class,
    ],

    'middlewareAliases' => [
        'print' => HelloMiddleware::class,
        'auth' => AuthMiddleware::class,
    ],
    
];