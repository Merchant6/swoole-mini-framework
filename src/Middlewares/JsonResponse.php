<?php

namespace App\Middlewares;

use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;

class JsonResponse extends Middleware
{ 
    public function handle(Request $request, Response $response, Closure $next): Response
    {
        $response->header('Content-Type', 'application/json');
        return $next($request, $response);
    }
}
