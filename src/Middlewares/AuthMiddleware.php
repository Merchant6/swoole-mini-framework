<?php

namespace App\Middlewares;
use Closure;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Core\Response as Responser;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request, Response $response, Closure $next): Response
    {
        $authQueryParam = $request->get['auth'] ?? null;

        if ($authQueryParam !== 'true') {

            $response->status(401);
            $response->end(json_encode('Unauthorized'));
        }

        return $next($request, $response);
    }
}
