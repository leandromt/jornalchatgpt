<?php

namespace App\Services\ChatGPT;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\ChatGPT\Client;

class Actions
{
    private $client;
    private $body = [];

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Pega os beneficionarios num determinado período
     * 
     * @return string
     */
    public function getModels()
    {
        $res = $this->client->request('models', [], 'GET');

        if (empty(json_decode($res))) {
            return false;
        }

        $res = json_decode($res);

        return $res;
    }

    /**
     * Pega os beneficionarios num determinado período
     * 
     * @var array $data
     * @return mix
     */
    public function getChatCompletations($data)
    {
        $res = $this->client->request('chat/completions', $data, 'POST');

        if (empty(json_decode($res))) {
            return false;
        }

        $res = json_decode($res);

        return $res;
    }
}
