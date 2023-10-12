<?php

namespace App\Controllers;
use App\Entity\Entity;
use App\Entity\Swoole;
use App\Utils\Paginator;
use Swoole\Http\Request;
use App\Core\Response;

class ExampleController extends BaseController
{
    public function __construct(private Request $request)
    {
        
    }

    public function index()
    {
        $setter = (new Swoole())
        ->setProperties([
            'fname' => 'using',
            'lname' => 'setProperties'
        ]);

        return Response::json([
            'data' => $setter,
        ], 200);
    }

    public function get()
    {
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
        return Response::json(['data' => $query], 200);
    }
}