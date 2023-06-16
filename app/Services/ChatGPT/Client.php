<?php

namespace App\Services\ChatGPT;

use Exception;
use Illuminate\Support\Facades\Log;

class Client
{

    private $url = "https://api.openai.com/v1/";

    /**
     *
     * @var
     * no PHP 7.4 resource, no 8x cURL handle
     */
    private $ch;

    /**
     * Secret Key API ChatGPT
     * 
     * @var string
     */
    private $token;

    public function __construct()
    {
        $this->ch = curl_init();
        $this->token = config('services.chatgpt.scret_key');
    }

    /**
     * Realiza a requisição via curl
     * request
     *
     * @param string $url URL da consulta
     * @param array $body body da consulta que irá em json
     * @param string $method Tipo da consulta http. Padrão: POST
     * @return string
     */
    public function request($endpoint, $data = [], $method = 'POST')
    {
        // try {

        curl_setopt($this->ch, CURLOPT_URL, $this->url . $endpoint);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST') {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            echo 'Error:' . curl_error($this->ch);
        }

        curl_close($this->ch);

        return $response;


        /*
            curl_setopt($this->ch, CURLOPT_URL, $url);
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
            // curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($body));

            $res = curl_exec($this->ch);

            if (curl_errno($this->ch) !== 0) {
                throw new \Exception(curl_error($this->ch), curl_errno($this->ch));
            }

            $status_code = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);

            if (!in_array($status_code, [200, 201])) {
                throw new Exception("Erro API ChatGPT (Code: $status_code): $res");
            }

            return $res;
        } catch (Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return json_encode([
                'status' => 'error',
                'message' => 'Houve um erro. Tente novamente',
            ]);
        }
        */
    }
}
