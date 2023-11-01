<?php

use App\Controllers\ExampleController;

return [

    ['GET', '/', [ExampleController::class, 'index']],
    ['GET', '/get', [ExampleController::class, 'get']],
    ['POST', '/form', [ExampleController::class, 'form']],
];