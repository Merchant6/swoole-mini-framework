<?php

namespace App\Controllers;
use App\Core\CoroutineManager;
use App\Core\JsonResponse;
use App\Utils\Validator;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;
use Swoole\Http\Request;
class ExampleController extends BaseController
{
    public function __construct(private Request $request)
    {
        
    }

    public function index(string $name, JsonResponse $jsonResponse)
    {
        $msg = "Hello from $name";

        return JsonResponse::json([
            'data' => $msg,
        ], 200);
    }

    public function get(Entity $entity)
    {   
        $result = CoroutineManager::run(1, function () use($entity){
            $query = $entity->builder()
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

        return JsonResponse::json(['data' => $result], 200);
    }

    public function form(Validator $validator)
    {
        $validator->make($this->request, [
            'number'=> 'regex:/^\\d+$/',
        ]);

        if($validator->isValid())
        {
            return JsonResponse::json([
                'data' => 'Number Validated',
            ], 200);
        }

        return JsonResponse::json([
            'message' => $validator->getErrors(),
        ], 400);
    }
}