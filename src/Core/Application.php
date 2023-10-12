<?php

namespace App\Core;
use App\Controllers\BaseController;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Application
{
    /**
     * @var Router
     */
    public Router $router;

    /**
     * @var BaseController
     */
    public BaseController $baseController;

    public function __construct(public Request $request, public Response $response)
    {
        //Initailizing the Router class
        $this->router = new Router($request, $response);
    }

    public function run(): void
    {
        $this->router->router();
    }
}
