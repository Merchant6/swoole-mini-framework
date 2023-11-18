<?php

namespace App\Core;
use App\Controllers\BaseController;
use DI\Container;
use Swoole\Http\Response;
use Swoole\Http\Request;

class Application
{
    /**
     * @var Router
     */
    private Router $router;

    public function __construct()
    {   
        
    }

    /**
     * Summary of handle
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @param \DI\Container $container
     * @return void
     */
    public function handle(Request $request, Response $response, Container $container)
    {    
        //Applying the middleware
        $this->applyRequestMiddlewares($request, $response, $container);
        
        //Initializing the router
        $this->router = new Router($request, $response, $container);
    }

    private function applyRequestMiddlewares($request, $response, $container): void
    {
        (new MiddlewareDispatcher($request, $response, $container))
        ->dispatchRequestMiddleware();
    }

    public function run(): void
    {
        $this->router->router();
    }
}
