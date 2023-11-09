<?php

namespace App\Core;
use DI\Container;
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
    public function handle(Request $request, Response $response, Container $container)
    {
        //Applying the middleware
        $this->applyRequestMiddlewares($request, $response);

        //Need to initialize the DI container for method injection
        
        //Initializing the router
        $this->router = new Router($request, $response, $container);
    }

    private function applyRequestMiddlewares($request, $response): void
    {
        (new MiddlewareDispatcher($request, $response))
        ->dispatchRequestMiddleware();
    }


    public function run(): void
    {
        $this->router->router();
    }
}
