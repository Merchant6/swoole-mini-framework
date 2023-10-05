<?php

namespace App\Core;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Application
{
    public Router $router;

    public function __construct(public Request $request, public Response $response)
    {
        $this->router = new Router($request, $response);
    }
}
