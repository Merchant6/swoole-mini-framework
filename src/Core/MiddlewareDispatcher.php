<?php

namespace App\Core;

use Swoole\Http\Request;
use Swoole\Http\Response;

class MiddlewareDispatcher
{   
    public function __construct(private Request $request, protected Response $response)
    {

    }

    /**
     * Dispatch middlewares defined in the middlewareConfig.php 
     * @return Response
     */
    public function dispatch(): Response
    {   
        $config = require(__DIR__ . '/../Config/middlewareConfig.php');

        $next = function(Request $request, Response $response) {
            return $response;
        };

        foreach (array_reverse($config) as $middlewareClass) {
            $currentMiddleware = new $middlewareClass();
            $nextMiddleware = $next;
            $next = function (Request $request, Response $response) use ($currentMiddleware, $nextMiddleware) {
                return $currentMiddleware->handle($request, $response, $nextMiddleware);
            };
        }
        
        $firstMiddleware = new $config[0]();
        $firstMiddleware->handle($this->request, $this->response, $next);
        
        return $this->response;

    }
}
