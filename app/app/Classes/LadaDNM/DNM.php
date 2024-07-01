<?php

namespace App\Classes\LadaDNM;

use Illuminate\Support\Facades\Http;

class DNM
{
    private const TOKEN = 'SCDJCHFpIzAad7WMM6XYv1biG86T3Qru4OZPUK1utkzQ3y2TrW7Z1O6HJEwNZKfcFOLGo9Tj66dzSLEoS4CkZAAXakDULS6oagxduMb2To_cn_01lw_qybtpUsT74Erk';

    private const BASE_URL = 'https://vaz-test.autocrm.ru';

    public $client;

    public function __construct()
    {
        $headers = [
            'Authorization' => 'Bearer ' . self::TOKEN,
            'Accept' => 'application/json',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $this->client = Http::withHeaders($headers);
    }



    public function sendPost(string $url, array $data)
    {
        return $this->client->post($this->concatUrl($url), $data);
    }



    public function sendPut(string $url, array $data)
    {
        return $this->client->put($this->concatUrl($url), $data);
    }



    public function sendGet(string $url, array $data)
    {
        return $this->client->get($this->concatUrl($url), $data);
    }



    private function concatUrl($getParam)
    {
        $getParam = trim($getParam, '/');

        return self::BASE_URL . '/' . $getParam;
    }



    public function getBrands()
    {
        return $this->client->get($this->concatUrl('api/brand'))->json();
    }



    public function getModelAliases()
    {
        return $this->client->get($this->concatUrl('api/model-alias'))->json();
    }



    public function getModelYears()
    {
        return $this->client->get($this->concatUrl('api/model-year'))->json();
    }



    public function getPositions()
    {
        return $this->client->get($this->concatUrl('api/position'))->json();
    }



    public function getManagers()
    {
        return $this->client->get($this->concatUrl('/api/manager'))->json();
    }



    public function getSources()
    {
        return $this->client->get($this->concatUrl('/api/source'))->json();
    }



    public function getEvents()
    {
        return $this->client->get($this->concatUrl('api/event-type'))->json();
    }
}
