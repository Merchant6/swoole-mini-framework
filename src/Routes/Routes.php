<?php

namespace App\Routes;
use App\Controllers\ExampleController;
use FastRoute\RouteCollector;
use Swoole\Http\Request;

class Routes
{
    public function __construct(private Request $request , private RouteCollector $route)
    {

    }

    /**
     * Define your routes here
     * @return array
     */
    public function define(): array
    {
        return [

            $this->route->get('/', [new ExampleController($this->request), 'index']),
            $this->route->get('/get', [new ExampleController($this->request), 'get'])

        ];
    }
}
