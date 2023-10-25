<?php

namespace App\Core;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Application
{
    /**
     * @var Router
     */
    public Router $router;

    public function __construct()
    {   
        
    }


    /**
     * Handle the current Request
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @return void
     */
    public function handle(Request $request, Response $response)
    {
        //Applying the middleware
        $this->applyMiddlewares($request, $response);
        
        //Initializing the router
        $this->router = new Router($request, $response);
    }

    private function applyMiddlewares($request, $response): void
    {
        (new MiddlewareDispatcher($request, $response))
        ->dispatch();
    }


    public function run(): void
    {
        $this->router->router();
    }
}
