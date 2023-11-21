<?php

namespace App\Core;
use Exception;
use Swoole\Http\Request;

class RequestContext
{
    protected Request $request; 

    public function __construct(CoroutineContext $coroutineContext)
    {
        $this->request = $coroutineContext->get('request');
    }

    public function getInstance()
    {
        return $this->request;
    }

    public function server(array $data = null): array|Exception
    {
        if(!$data)
        {
            return $this->request->server;
        }

        $serverInfo = [];
        foreach($data as $dataKey)
        {
            if(!array_key_exists($dataKey, $this->request->server))
            {
                throw new Exception("Key '$dataKey' does not exist.");
            }

            $serverInfo[$dataKey] = $this->request->server[$dataKey];
        }

        return $serverInfo;
    }

    public function get(array $data = null): array|Exception
    {
        if(!$data)
        {
            return $this->request->get;
        }

        $requestData = [];
        foreach($data as $dataKey)
        {
            if(!array_key_exists($dataKey, $this->request->get))
            {
                throw new Exception("Key '$dataKey' does not exist.");
            }

            $requestData[$dataKey] = $this->request->get[$dataKey];
        }

        return $requestData;
    }

    public function post(array $data = null): array|Exception
    {
        if(!$data)
        {
            return $this->request->post;
        }

        $postData = [];
        foreach($data as $dataKey)
        {
            if(!array_key_exists($dataKey, $this->request->post))
            {
                throw new Exception("Key '$dataKey' does not exist.");
            }

            $postData[$dataKey] = $this->request->post[$dataKey];
        }

        return $postData;
    }

    public function getData()
    {
        return $this->request->getData();
    }
}

