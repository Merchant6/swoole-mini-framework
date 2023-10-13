<?php

namespace App\Routes;
use App\Controllers\ExampleController;
use FastRoute\RouteCollector;
use Swoole\Http\Request;

class Routes
{
    public function __construct(protected Request $request , protected RouteCollector $route)
    {

    }

    public function routes()
    {
        return [

            $this->route->get('/', [new ExampleController($this->request), 'index']),
            $this->route->get('/get', [new ExampleController($this->request), 'get'])
        ];
    }
}
