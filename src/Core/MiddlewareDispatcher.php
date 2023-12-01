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

    private RouteCollection $routeCollection;

    public function __construct(private Request $request, private Response $response, private Container $container)
    {
        $this->configFile = require(__DIR__ . '/../Config/middlewareConfig.php');
        $this->routeCollection = new RouteCollection();
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

    /*
    * Takes an array of routes from the Routes.php file and 
    * apply route middlewares if the current request URI
    * matches the current route URI
    */
    public function routeMiddlewares(array $routes): void
    {
        $config = $this->configFile['middlewareAliases'];

        foreach ($routes as $route) {
            if ($this->matchesRoute($route)) 
            {
                $middlewareArray = $route[3] ?? null;
                if(isset($middlewareArray) && is_array($middlewareArray)) 
                {
                    foreach ($middlewareArray as $middlewareAlias) {
                        if (array_key_exists($middlewareAlias, $config)) {
                            $middlewareClass = $config[$middlewareAlias];
                            $currentMiddleware = new $middlewareClass;

                            // Apply middleware only if the route matches
                            $response = $currentMiddleware->handle($this->request, $this->response, function ($request, $response) use ($route) {
                                $this->invokeControllerMethod($route);
                                return $response;
                            });

                            $this->response = $response;
                        }
                    }
                }
                break;
            }
        }
    }

    /*
    * Invoke the appropriate controller method if 
    * the middleware is applied 
    */
    private function invokeControllerMethod(array $route): void
    {
        $controller = $route[2][0];
        $method = $route[2][1];

        $controllerClass = $this->container->get($controller);
        $reflectMethod = new \ReflectionMethod($controllerClass, $method);
        $methodParams = $reflectMethod->getParameters();

        if ($methodParams) 
        {
            $resolvedParams = [];
            foreach ($methodParams as $param) {
                if (!$param->getType()->isBuiltIn() && is_object($param)) 
                {
                    $paramName = $param->getName();
                    $className = $param->getType()->getName();
                    $classInstance = $this->container->get($className);
                    $resolvedParams[$paramName] = $classInstance;

                } else {
                    $resolvedParams[] = $param->getName();
                }
            }

            $reflectMethod->invokeArgs($controllerClass, $resolvedParams);
        } else {
            $controllerClass->$method();
        }
    }

    /*
    * Match the current route uri with current request uri 
    */
    public function matchesRoute($route)
    {
        $requestUri = $this->request->server['request_uri'];
        $routeMethod = $route[0];
        $routeUri = $route[1];

        if ($this->request->getMethod() == $routeMethod) 
        {
            if (str_contains($routeUri, "{") && str_contains($routeUri, "}")) 
            {
                // This will create a regex from the route URI
                $regexFromRoute = preg_replace_callback('#\{([^:]+):([^\}]+)\}#', function ($matches) {
                    
                    // Use the specified pattern or a default pattern if not specified
                    $pattern = isset($matches[2]) ? $matches[2] : '[^/]+';

                    // Return the replacement pattern
                    return $pattern;

                }, $routeUri);
    
                // Added hashtag(#) as a delimiter for the regex
                return (preg_match("#$regexFromRoute#", $requestUri));

            } else {
                // No placeholders, treat it as an exact match
                return ($requestUri === $routeUri);
            }
        }
    
    }

}
