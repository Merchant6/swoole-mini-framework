<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\Hello;
use App\Middlewares\HelloMiddleware;
use App\Middlewares\JsonResponseMiddleware;

return [

    'middleware' => [
        JsonResponseMiddleware::class,
    ],

    'middlewareAliases' => [
        'print' => HelloMiddleware::class,
        'auth' => AuthMiddleware::class,
    ],
    
];