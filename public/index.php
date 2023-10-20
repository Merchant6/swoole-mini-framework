<?php


/*
|--------------------------------------------------------------------------
| Bootstrapping the app
|--------------------------------------------------------------------------
|
*/
require_once __DIR__ . '/../bootstrap.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Core\Application;

//Setting up the App Url and App Port
$appUrl = $_ENV['APP_URL'];
$appPort = $_ENV['APP_PORT'];

//Initializing the Server
$server = new Server($appUrl, $appPort);

$server->set([
    'enable_coroutine' => true,
]);

//Display information on server start
$server->on("start", function (Server $server) use($appUrl, $appPort) {
    echo "Swoole http server is started at $appUrl:$appPort\n";
});

//Can now handle requests
$server->on("request", function (Request $request, Response $response) {

    //Initializing the Application
    $app = new Application($request, $response);

    //Run the application
    $app->run();
    
});

//Starting up the server
$server->start();