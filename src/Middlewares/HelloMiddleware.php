<?php

namespace App\Middlewares;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Closure;

class HelloMiddleware extends Middleware
{
    public function handle(Request $request, Response $response, Closure $next): Response
    {
        // var_dump("Hello from route Middleware.");

        return $next($request, $response);
    }
}
