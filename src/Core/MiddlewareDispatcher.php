<?php

namespace App\Core;

use App\Controllers\ExampleController;
use App\Routes\Routes;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;

class MiddlewareDispatcher
{   
    protected $configFile;

    public function __construct(private Request $request, protected Response $response)
    {
        $this->configFile = require(__DIR__ . '/../Config/middlewareConfig.php');
    }

    /**
     * Dispatch middlewares defined in the middlewareConfig.php 
     * @return Response
     */
    public function dispatchRequestMiddleware(): Response
    {   
        $config = $this->configFile['middleware'];

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

    public function routeMiddlewares(array $routes): void
    {   
        // var_dump($routes);
    }
}
