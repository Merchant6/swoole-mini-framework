<?php

namespace App\Core;
use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Core\JsonResponse;

use function FastRoute\simpleDispatcher;

class Router
{

    public function __construct(protected Request $request, protected Response $response, protected Container $container)
    {

    }

    public function router() : void
    {
        /*
        |--------------------------------------------------------------------------
        | Implementing FastRoute simple dispatcher
        |--------------------------------------------------------------------------
        */
        $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector){

            $routes = include __DIR__ . '/../Routes/Routes.php';
            
            //Applying Route Middlewares
            (new MiddlewareDispatcher($this->request, $this->response))->routeMiddlewares($routes);

            foreach ($routes as $route) {
                // Add the route handler with applied middlewares
                $routeCollector->addRoute($route[0], $route[1], [new $route[2][0]($this->request), $route[2][1]]);
            }
        });

        $httpMethod = $this->request->server['request_method'];
        $uri = rawurldecode($this->request->server['request_uri']);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $this->response->status(404);
                $this->response->end('Not Found');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                $this->response->status(405);
                $this->response->end('Method not allowed');
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $reflector = new MethodInvoker(new $handler[0]($this->request), $handler[1], $vars, $this->container);
                $invoke = $reflector->invoke();

                // var_dump($handler[0]);

                $responseContent = $invoke;
                if($this->response->isWritable()) 
                {
                    $this->response->status(JsonResponse::$status ?? 200);
                    $this->response->end($responseContent);
                }

                break;
        }
    }
}
