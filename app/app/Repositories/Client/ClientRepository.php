<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Http\Filters\ClientFilter;
use App\Models\Trafic;
use Illuminate\Support\Arr;
use App\Models\ClientPassport;

/**
 * Репазиторий модели Client
 */
Class ClientRepository
{
    /**
     * Метод задает запрос на получение списка клиентов удовлетворяющих заданным свойствам фильтра
     * @param array $data данные для фильтра
     * @param integer $paginate количество клиентов на итерацию пагинации
     * @return \Illuminate\Support\Collection $clients Illuminate\Support\Collection
     */
    public function filter($data = [], $paginate = 50) : \Illuminate\Support\Collection
    {
        $query = Client::select('clients.*');
        $filter = app()->make(ClientFilter::class, ['queryParams' => array_filter($data)]);
        $clients = $query->filter($filter)->paginate($paginate);
        return $clients;
    }

    /**
     * Метод сохранения клиента в бд,используется как для создания, так и для изменения
     * @param Client $client содержит модель клиента, может быть пустой в случае создания
     * @param array $data данные полученые с фронта для заполнения модели клиента
     * @return Client $client
     */
    public function save(Client $client, $data = []) : Client
    {
        $columns = Arr::except(Client::getColumnsName(), ['id']);
        $client->fill(Arr::only($data, $columns))->save();

        foreach($data['contacts'] as $itemRowContact) {
            if($itemRowContact['phone'])
                $client->phones()->updateOrCreate(['client_id' => $client->id, 'phone' => $itemRowContact['phone']], [
                    'phone'=>$itemRowContact['phone']
                ]);
            if($itemRowContact['email'])
                $client->emails()->updateOrCreate(['client_id' => $client->id, 'email' => $itemRowContact['email']], [
                    'email'=>$itemRowContact['email']
                ]);
        }

        $passportData = Arr::only($data, ClientPassport::getColumnsName());
        $passportData['birthday_at'] =              $passportData['birthday_at'] ? date('Y-m-d',\strtotime($passportData['birthday_at'])) : NULL;
        $passportData['driver_license_issue_at'] =  $passportData['driver_license_issue_at'] ? date('Y-m-d',\strtotime($passportData['driver_license_issue_at'])) : NULL;
        $passportData['passport_issue_at'] =        $passportData['passport_issue_at'] ? date('Y-m-d',\strtotime($passportData['passport_issue_at'] )) : NULL;
        $client->passport()->updateOrCreate(['client_id' => $client->id], $passportData);

        return $client;
    }

    /**
     * Метод создания или получения клиента из бд, используется при создании рабочего листа Worksheet из трафика Trafic
     * @param Trafic $trafic содержит модель клиента, может быть пустой в случае создания
     * @return Client $client
     */
    public function findOrCreate(Trafic $trafic) :Client
    {
        $client = Client::with(['phones' => function($query) use ($trafic){
            $query->where('phone', $trafic->phone);
        }])->firstOrCreate([
            'lastname'      => $client->lastname ?? $trafic->lastname,
            'firstname'     => $client->firstname ?? $trafic->firstname,
            'fathername'    => $client->fathername ?? $trafic->fathername
        ]);

        if($client->wasRecentlyCreated) {
            $client->phones()->create([
                'phone' => $trafic->phone
            ]);
            $client->emails()->create([
                'email' => $trafic->email
            ]);
        }
        return $client;
    }
}
