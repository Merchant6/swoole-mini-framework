<?php

namespace App\Middlewares;
use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;


class Hello
{
    public function handle(mixed $responseContent, Response $response, Closure $next): Response
    {
        var_dump($responseContent);
        return $next($response);
    }
}
