<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ExampleController;
use App\Controllers\HelloController;
use FastRoute\RouteCollector;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use function FastRoute\simpleDispatcher;

$server = new Server("127.0.0.1", 9501);

$server->on("start", function (Server $server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$server->on("request", function (Request $request, Response $response) {

    //Create a dispatcher
    $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector){
        $routeCollector->get('/', [new ExampleController, 'index']);
    });

    $response->header("Content-Type", "text/plain");
    // $response->end(ExampleController::class);

    // Fetch method and URI from somewhere
    $httpMethod = $request->server['request_method'];
    $uri = rawurldecode($request->server['request_uri']);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found
            $response->status(404);
            $response->end('Not Found');
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            $response->status(405);
            $response->end('Method not allowed');
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            $responseText = $handler($vars);
            $response->end($responseText);
            break;
    }
});

$server->start();