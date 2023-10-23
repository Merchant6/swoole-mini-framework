<?php

namespace App\Core;
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;

class CoroutineManager
{
    protected Co $coroutine;

    public function __construct()
    {
        $this->coroutine = new Co();
    }


    /**
     * Creare a Coroutine with a channel capacity
     * @param int $capacity
     * @param callable $callable
     * @return array
     */
    public static function run(int $capacity = 1, callable $callable): array
    {
        $channel = new Channel($capacity);
        Co::create(function () use ($callable, $channel) 
        {
            $output = $callable();
            $channel->push($output);
        });

        return $channel->pop();
    }

    public static function stats()
    {
        return Co::stats();
    }

    public static function getId()
    {
        return Co::getCid();
    }
    
}
