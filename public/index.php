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

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Dotenv\Dotenv;
use App\Core\Application;

//Loading the Dotenv
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$appUrl = $_ENV['APP_URL'];
$appPort = $_ENV['APP_PORT'];



$server = new Server($appUrl, $appPort);

$server->on("start", function (Server $server) use($appUrl, $appPort) {
    echo "Swoole http server is started at $appUrl:$appPort\n";
});

$server->on("request", function (Request $request, Response $response) {

    $app = new Application($request, $response);
    $app->router->router();
    
});

$server->start();