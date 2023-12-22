<?php

namespace App\Controllers;
use App\Core\ConnectionPool;
use App\Core\JsonResponse;
use App\Core\RequestContext;
use App\Utils\Validator;
use App\Entity\Swoole;
use App\Utils\Paginator;

class ExampleController extends BaseController
{    

    
    public function __construct()
    {
    
    }

    public function index(string $name, RequestContext $r)
    {
        
    }

    public function get(ConnectionPool $pool, RequestContext $r)
    {   
        $pooledQuery = $pool->run(function($conn){

            $query = $conn->createQueryBuilder()
            ->select('s')    
            ->addSelect(['s.fname', 's.lname'])
            ->from(Swoole::class, 's'); 

            return $query;
            
        });

        $paginator = (new Paginator())
        ->paginate($pooledQuery, $r->getInstance()->get['page'] ?? 1);
            
        $result = [];
        foreach($paginator->getItems() as $p)
        {
            $result[] = [
                'fname' => $p['fname'],
                'lname' => $p['lname'],
            ];
        }

        return JsonResponse::make([
            'data' => $result
        ], 200);
        
    }

    public function pooled(RequestContext $requestContext)
    {
        
    }

    public function form(Validator $validator, RequestContext $request)
    {
        
    }
}