<?php

namespace App\Core;

use Swoole\Http\Request;
use Swoole\Http\Response;

class MiddlewareDispatcher
{   
    private array $config;

    public function __construct(private Request $request, protected Response $response)
    {

    }

    public function dispatch()
    {   
        $config = require(__DIR__ . '/../Config/middlewareConfig.php');

        if(!is_bool($config))
        {
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

        return;

    }
}
