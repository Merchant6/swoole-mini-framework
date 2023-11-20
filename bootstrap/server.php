<?php

use App\Core\CoroutineContext;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use DI\ContainerBuilder;


$app = require_once __DIR__ . '/app.php';

//Setting up the App Url and App Port
$appUrl = $_ENV['APP_URL'];
$appPort = $_ENV['APP_PORT'];

//Initializing the Server
$server = new Server($appUrl, $appPort);

$server->set([
    'enable_coroutine' => true,
]);

//Initializing the container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/Config/definitions.php');

$containerBuilder->useAutowiring(true);
$container = $containerBuilder->build();

//Display information on server start
$server->on("start", function (Server $server) use($appUrl, $appPort) {
    echo "Swoole http server is started at $appUrl:$appPort\n";
});

//Can now handle requests
$server->on("request", function (Request $request, Response $response) use($app, $container) {

    //Setting Coroutine Context for per Request DI
    CoroutineContext::set('request', $request);

    //Handling the request
    $app->handle($request, $response, $container);

    //Run the application
    $app->run();
    
});

//Starting up the server
$server->start();