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

    public function routeMiddlewares(array $routes)
    {   
        $config = $this->configFile['middlewareAliases'];

        foreach($routes as $route)
        {   
            $path  = $route[1];
            $controller = $route[2][0];
            $method = $route[2][1];

            $middlewareArray = $route[3] ?? null;

            if(!isset($middlewareArray))
            {
                continue;
            }

            if(isset($middlewareArray) && is_array($middlewareArray))
            {   
                foreach($middlewareArray as $middlewareAlias)
                {
                    if(array_key_exists($middlewareAlias, $config))
                    {
                        $middlewareClass = $config[$middlewareAlias];
                        $currentMiddleware = new $middlewareClass;
                        
                        if($this->matchesRoute($path))
                        {
                            $response = $currentMiddleware->handle($this->request, $this->response, function ($request, $response) use ($controller, $method) {
                            
                                $controller = new $controller($this->request);
    
                                $reflectMethod  = new \ReflectionMethod($controller, $method);
                                $methodParams = $reflectMethod->getParameters();
                                if(!$methodParams || !$methodParams = null)
                                {
                                    call_user_func_array([$controller, $method], $methodParams);
                                }
    
                                $controller->$method();
    
                                return $response;
                            });
        
                            $this->response = $response;
                        }
                    }
                };
            }
        }
    }

    public function matchesRoute($routeUri): bool
    {
        $currentUri = $this->request->server['request_uri'];
        if($currentUri == $routeUri)
        {
            return true;
        }

        return false;
    }
}
