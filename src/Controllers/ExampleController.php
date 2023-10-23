<?php

namespace App\Controllers;
use App\Core\CoroutineManager;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;
use Swoole\Http\Request;
use App\Core\Response;
use Swoole\Coroutine as co;
use Swoole\Coroutine\Channel;

class ExampleController extends BaseController
{
    public function __construct(private Request $request)
    {
        
    }

    public function index()
    {
        $msg = 'Hello from routes';

        return Response::json([
            'data' => $msg,
        ], 200);
    }

    public function get()
    {   
        $result = CoroutineManager::run(1, function () {
            $query = Entity::builder()
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

        return Response::json(['data' => $result], 200);
    }
}