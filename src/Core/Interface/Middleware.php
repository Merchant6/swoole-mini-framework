<?php

namespace App\Core\Interface;
use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;

interface Middleware
{
    public function handle(Request $request, Response $response, Closure $next);
}
