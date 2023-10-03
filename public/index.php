<?php


/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ExampleController;
use App\Controllers\HelloController;
use FastRoute\RouteCollector;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Dotenv\Dotenv;

use function FastRoute\simpleDispatcher;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$appUrl = $_ENV['APP_URL'];
$appPort = $_ENV['APP_PORT'];

$server = new Server($appUrl, $appPort);

$server->on("start", function (Server $server) use($appUrl, $appPort) {
    echo "Swoole http server is started at $appUrl:$appPort\n";
});

$server->on("request", function (Request $request, Response $response) {

    /*
    |--------------------------------------------------------------------------
    | Implementing FastRoute simple dispatcher
    |--------------------------------------------------------------------------
    */
    $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector){
        $routeCollector->get('/', [new ExampleController, 'index']);
    });

    $response->header("Content-Type", "text/plain");

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