<?php

namespace App\Controllers;
use App\Config\DbConfig;
use App\Core\Response;
use App\Database\DB;
use App\Entity\Entity;
use App\Entity\Swoole;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Swoole\Http\Request;
use App\Utils\Paginator;

class ExampleController extends BaseController
{
    public function __construct(public Request $request)
    {
        
    }

    public function index()
    {
        $set = (new Swoole)
        ->setFname('again')
        ->setLname('inserting');

        Entity::persistAndFlush($set);

        return Response::json(['data' => $set->getId()], 200);
    }

    public function get()
    {
        $query = Entity::builder()
        ->select('s')    
        ->addSelect(['s.fname', 's.lname'])
        ->from(Swoole::class, 's'); 

        $paginator = (new Paginator())
        ->paginate($query, $this->request->get['page'] ?? 1);

        $result = [];
        foreach($paginator->getItems() as $p)
        {
            $result[] = [
                'fname' => $p['fname'],
                'lname' => $p['lname'],
            ];
        }
        return Response::json(['data' => $result], 200);
    }
}