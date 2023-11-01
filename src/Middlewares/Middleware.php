<?php

namespace App\Middlewares;

use App\Core\Interface\Middleware as MiddlewareInterface;
use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Middleware implements MiddlewareInterface
{   
    public function handle(Request $request, Response $response, Closure $next)
    {
        //Todo
    }
}
