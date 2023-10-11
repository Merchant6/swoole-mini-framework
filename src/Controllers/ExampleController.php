<?php

namespace App\Controllers;
use App\Core\Response;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;

class ExampleController extends BaseController
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $text = "Hello from swoole-mini";

        return Response::json([$text], 200);
    }

    public function get()
    {
        $query = Entity::builder()
        ->select('s')    
        ->addSelect(['s.id', 's.fname', 's.lname'])
        ->from(Swoole::class, 's'); 

        $paginator = (new Paginator())
        ->paginate($query, $this->request->get['page'] ?? 1);

        $result = [];
        foreach($paginator->getItems() as $p)
        {
            $result[] = [
                'id'    => $p['id'],
                'fname' => $p['fname'],
                'lname' => $p['lname'],
            ];
        }
        return Response::json(['data' => $result], 200);
    }
}