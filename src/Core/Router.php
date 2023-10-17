<?php

namespace App\Core;
use App\Controllers\ExampleController;
use App\Routes\Routes;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;

use function FastRoute\simpleDispatcher;

class Router
{

    public function __construct(public Request $request, public Response $response)
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

            $routes = new Routes($this->request, $routeCollector);
            return $routes->define();
        });

        $httpMethod = $this->request->server['request_method'];
        $uri = rawurldecode($this->request->server['request_uri']);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $this->response->status(404);
                // $this->response->header('Content-Type', 'application/json');
                $this->response->end('Not Found');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                $this->response->status(405);
                // $this->response->header('Content-Type', 'application/json');
                $this->response->end('Method not allowed');
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $responseText = $handler($vars);
                if ($responseText !== null) 
                {   
                    $this->response->status(\App\Core\Response::$status ?? 200);
                    $this->response->end($responseText);
                } 

                break;
        }
    }
}
