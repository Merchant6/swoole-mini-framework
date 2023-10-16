<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\JsonResponse;

return [
    JsonResponse::class,
    AuthMiddleware::class,
];