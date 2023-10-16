<?php

namespace App\Core;
use App\Controllers\BaseController;
use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Application
{
    /**
     * @var Router
     */
    public Router $router;

    public function __construct(public Request $request, public Response $response)
    {   
        //Applying the middleware
        $this->applyMiddlewares();
        
        //Initializing the router
        $this->router = new Router($request, $response);
    }

    private function applyMiddlewares(): void
    {
        (new MiddlewareDispatcher($this->request, $this->response))
        ->dispatch();
    }


    public function run(): void
    {
        $this->router->router();
    }
}
