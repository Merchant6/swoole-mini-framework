<?php

namespace App\Core;

use App\Controllers\ExampleController;
use App\Routes\Routes;
use DI\Container;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;

class MiddlewareDispatcher
{   
    protected $configFile;

    public function __construct(private Request $request, private Response $response, private Container $container)
    {
        $this->configFile = require(__DIR__ . '/../Config/middlewareConfig.php');
    }

    /**
     * Dispatches the global middleware stack defined in middlewareConfig.php
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
        
        // $firstMiddleware = $this->container->get($config[0]);
        $firstMiddleware = new $config[0]($this->request, $this->response, $nextMiddleware);
        $firstMiddleware->handle($this->request, $this->response, $next);

        return $this->response;

    }

    /**
     * Takes an array of routes from the Routes.php file and 
     * apply route middlewares if the current request URI
     * matches the current route URI
     *  
     * @param array $routes
     * @return void
     */
    public function routeMiddlewares(array $routes): void
    {   
        $config = $this->configFile['middlewareAliases'];

        foreach($routes as $route)
        {   
            $path  = $route[1];
            $controller = $route[2][0];
            $method = $route[2][1];

            $reflectController = new \ReflectionClass($controller);
            $controllerName = $reflectController->getName();
            $parentController = $reflectController->getParentClass()->getName();

            $middlewareArray = $route[3] ?? null;

            if(isset($middlewareArray) && is_array($middlewareArray))
            {   
                foreach($middlewareArray as $middlewareAlias)
                {
                    if(array_key_exists($middlewareAlias, $config))
                    {
                        $middlewareClass = $config[$middlewareAlias];
                        $currentMiddleware = new $middlewareClass;
                        
                        // if($this->matchesRoute($path))
                        // {
                            $response = $currentMiddleware->handle($this->request, $this->response, function ($request, $response) use ($parentController, $controllerName, $method) 
                            {
                                $controllerClass = $this->container->get($controllerName);
    
                                $reflectMethod  = new \ReflectionMethod($controllerClass, $method);
                                $methodParams = $reflectMethod->getParameters();
                                if($methodParams)
                                {
                                    $reflectMethod->invokeArgs($controllerClass, $methodParams);
                                    return $response;
                                }
                                
                                $controllerClass->$method();
                                return $response;
                            });
        
                            $this->response = $response;
                        }
                    // }
                }
            }
            else
            {
                continue;
            }
        }
    }

    /**
     * Matches the current route URI with the current request URI
     * @param mixed $routeUri
     * @return bool
     */
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
