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

    
    public function __construct(private RequestContext $request)
    {
    
    }

    public function index(string $name, RequestContext $r)
    {
        $msg = "Hello from $name";

        return JsonResponse::make([
            'data' => $name,
        ], 200);
    }

    public function get(Entity $e)
    {   
        $result = CoroutineManager::run(1, function () use($e){
            $query = $e->builder()
            ->select('s')    
            ->addSelect(['s.id', 's.fname', 's.lname'])
            ->from(Swoole::class, 's');

            $paginator = (new Paginator())
            ->paginate($query, $this->request->get['page'] ?? 1, 20);

            $result = [];
            foreach($paginator->getItems() as $p)
            {
                $result[] = [
                    'id'    => $p['id'],
                    'fname' => $p['fname'],
                    'lname' => $p['lname'],
                ];
            }

            return $result;
        });

        return JsonResponse::make(['data' => $result], 200);
    }

    public function form(Validator $validator)
    {
        $validator->make($this->request, [
            'text'=> 'length:2:20|string',
            'email' => 'email'
        ]);

        if($validator->isValid())
        {
            return JsonResponse::make([
                'data' => 'Text Validated',
            ], 200);
        }

        return JsonResponse::make([
            'message' => $validator->getErrors(),
        ], 400);
    }
}