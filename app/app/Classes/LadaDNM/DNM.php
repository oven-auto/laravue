<?php

namespace App\Classes\LadaDNM;

use Illuminate\Support\Facades\Http;

class DNM
{
    private static $instance;

    private const TOKEN = 'SCDJCHFpIzAad7WMM6XYv1biG86T3Qru4OZPUK1utkzQ3y2TrW7Z1O6HJEwNZKfcFOLGo9Tj66dzSLEoS4CkZAAXakDULS6oagxduMb2To_cn_01lw_qybtpUsT74Erk';

    private const BASE_URL = 'https://vaz-test.autocrm.ru';

    public $service;

    // public function __construct()
    // {
    //     $headers = [
    //         'Authorization' => 'Bearer ' . self::TOKEN,
    //         'Accept' => 'application/json',
    //         'Connection' => 'keep-alive',
    //         'Content-Type' => 'application/json; charset=utf-8',
    //     ];

    //     $this->service = Http::withHeaders($headers);
    // }

    private function __construct()
    {
    }



    public static function init(): self
    {
        if (self::$instance !== null)
            return self::$instance;

        $me = new self;

        $headers = [
            'Authorization' => 'Bearer ' . self::TOKEN,
            'Accept' => 'application/json',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $me->service = Http::withHeaders($headers);

        return $me;
    }



    public function sendPost(string $url, array $data)
    {
        return $this->service->post($this->concatUrl($url), $data);
    }



    public function sendPut(string $url, array $data)
    {
        return $this->service->put($this->concatUrl($url), $data);
    }



    public function sendGet(string $url, array $data = [])
    {
        return $this->service->get($this->concatUrl($url), $data);
    }



    private function concatUrl($getParam)
    {
        $getParam = trim($getParam, '/');

        return self::BASE_URL . '/' . $getParam;
    }



    public function getBrands()
    {
        return $this->service->get($this->concatUrl('api/brand'))->json();
    }



    public function getModelAliases()
    {
        return $this->service->get($this->concatUrl('api/model-alias'))->json();
    }



    public function getModelYears()
    {
        return $this->service->get($this->concatUrl('api/model-year'))->json();
    }



    public function getPositions()
    {
        return $this->service->get($this->concatUrl('api/position'))->json();
    }



    public function getManagers()
    {
        return $this->service->get($this->concatUrl('/api/manager'))->json();
    }



    public function getSources()
    {
        return $this->service->get($this->concatUrl('/api/source'))->json();
    }



    public function getEvents()
    {
        return $this->service->get($this->concatUrl('api/event-type'))->json();
    }



    public function getResults()
    {
        return $this->service->get($this->concatUrl('/api/lms/result'))->json();
    }
}
