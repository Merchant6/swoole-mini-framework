<?php

namespace App\Core;

class RouteCollection
{
    public array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../Routes/Routes.php';
    }
}
