<?php

namespace App\Controllers;
use App\Core\CoroutineContext;
use App\Core\CoroutineManager;
use App\Core\JsonResponse;
use App\Core\RequestContext;
use App\Utils\Validator;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;
use Swoole\Http\Request;

class ExampleController extends BaseController
{    

    
    public function __construct()
    {
    
    }

    public function index(string $name, RequestContext $r)
    {
        
    }

    public function get(Entity $e, RequestContext $r)
    {   
        
    }

    public function form(Validator $validator, RequestContext $request)
    {
        
    }
}