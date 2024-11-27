<?php

namespace App\Classes\LadaDNM;

use Illuminate\Support\Facades\Http;

class DNM
{
    private static $instance;
    
    private static $token; 
    
    private static $baseUrl;

    public $service;



    private function __construct()
    {
    }



    public static function init(): self
    {
        if (self::$instance !== null)
            return self::$instance;
        
        self::$instance = new self;
        
        self::$token = env("DNM_TOKEN");

        self::$baseUrl = env("DNM_URL");

        $headers = [
            'Authorization' => 'Bearer ' . self::$token,
            'Accept' => 'application/json',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        self::$instance->service = Http::withHeaders($headers);

        return self::$instance;
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

        return self::$baseUrl . '/' . $getParam;
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