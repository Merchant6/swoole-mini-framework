<?php

use App\Controllers\ExampleController;

return [

    /*
    * Define your routes here 
    * ['METHOD', 'ROUTE', ['CONTROLLER', 'CONTROLLER-METHOD'], ['MIDDLEWARES(OPTIONAL)']]
    *
    */
    // ['GET', '/get', [ExampleController::class, 'get']],
    // ['POST', '/form', [ExampleController::class, 'form']],
    ['GET', '/{name:[a-z]+}', [ExampleController::class, 'index'], ['print', 'auth']],
];