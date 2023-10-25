<?php
use App\Core\Application;

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

// Loading the Dotenv
require_once __DIR__ . "/../src/Config/dotenv.php";

//Initializing the  Application
$app = new Application();

return $app;