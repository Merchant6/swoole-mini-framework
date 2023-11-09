<?php

use App\Entity\Entity;

use App\Entity\Swoole;
use function DI\create;

return [
    'Entity' => create(Entity::class),
    'Swoole' => create(Swoole::class)
];