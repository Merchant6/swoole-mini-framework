<?php

use App\Controllers\BaseController;
use App\Controllers\ExampleController;
use App\Controllers\HelloController;
use App\Core\Application;
use App\Core\CoroutineManager;
use App\Core\JsonResponse;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;
use App\Utils\Validator;
use Swoole\Http\Request;
use Swoole\Http\Response;

use function DI\autowire;
use function DI\create;

$rootNamespace = "App\\";
$rootPath = __DIR__ . '/../';

$subfolders = ['Controllers', 'Core', 'Database', 'Entity', 'Middlewares', 'Utils'];

$definition = [];

foreach ($subfolders as $subfolder) 
{   
    $subfolderPath = $rootPath . '/' . $subfolder;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($subfolderPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach($files as $file)
    {
        $fileName = pathinfo($file->getPathname(), PATHINFO_FILENAME);
        $classNamespace = $rootNamespace . $subfolder;
        $fqcn = $classNamespace . "\\" . $fileName ;

        if(class_exists($fqcn))
        {
            $definition[$fileName] = autowire($fqcn);
        }
    }
}

return $definition;