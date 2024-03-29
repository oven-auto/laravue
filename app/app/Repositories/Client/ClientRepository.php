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
     * @return \Illuminate\Database\Eloquent\Builder $query \Illuminate\Database\Eloquent\Builder
     */
    private function filter($data = []) :  \Illuminate\Database\Eloquent\Builder
    {
        $query = Client::select('clients.*');
        $filter = app()->make(ClientFilter::class, ['queryParams' => array_filter($data)]);
        return $query
            ->filter($filter);
    }

    /**
     * Метод возращает постраничную коллекию клиентов, прошедших фильтрацию
     * @param array $data данные для фильтра
     * @param integer $paginate не обязательное поле, по умолчанию 10
     * @return \Illuminate\Contracts\Pagination\Paginator $result \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate($data = [], $paginate = 10) : \Illuminate\Contracts\Pagination\Paginator
    {
        $query = $this->filter($data);
        $query->with(['latest_worksheet','phones','emails','cars','inn','zone','sex',]);
        $query->groupBy('clients.id');
        $result = $query->simplePaginate($paginate);
        return $result;
    }

    /**
     * Метод возращает коллекию клиентов (нет постраничного вывода!!!), прошедших фильтрацию
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection $result \Illuminate\Database\Eloquent\Collection
     */
    public function export($data = []) : \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->filter($data);
        $result = $query->get();
        return $result;
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


        $data['trafic_sex_id'] = $data['trafic_sex_id'] == 0 ? $data['trafic_sex_id'] = null : $data['trafic_sex_id'];
        $data['trafic_zone_id'] = $data['trafic_zone_id'] == 0 ? $data['trafic_zone_id'] = null : $data['trafic_zone_id'];

        $client->fill(Arr::only($data, $columns))->save();

        $client->phones()->delete();
        $client->emails()->delete();

        if(isset($data['inn']))
            $client->inn->fill([
                'number' => $data['inn']
            ])->save();

        foreach($data['contacts'] as $itemRowContact) {
            if(isset($itemRowContact['phone']) && $itemRowContact['phone'])
                $client->phones()->create(['client_id' => $client->id, 'phone' => preg_replace("/[^,.0-9]/", '', $itemRowContact['phone']) ]);
            if(isset($itemRowContact['email']))
                $client->emails()->create(['client_id' => $client->id, 'email' => $itemRowContact['email']]);
        }

        if($client->client_type_id == 1) {
            $passportData = Arr::only($data, ClientPassport::getColumnsName());
            $passportData['birthday_at'] =              isset($passportData['birthday_at']) ? date('Y-m-d',\strtotime($passportData['birthday_at'])) : NULL;
            $passportData['driver_license_issue_at'] =  isset($passportData['driver_license_issue_at']) ? date('Y-m-d',\strtotime($passportData['driver_license_issue_at'])) : NULL;
            $passportData['passport_issue_at'] =        isset($passportData['passport_issue_at']) ? date('Y-m-d',\strtotime($passportData['passport_issue_at'] )) : NULL;
            $passportData['client_id'] = $client->id;
            $client->passport->fill($passportData)->save();
        }

        return $client;
    }

    /**
     * Метод создания или получения клиента из бд, используется при создании рабочего листа Worksheet из трафика Trafic
     * @param Trafic $trafic содержит модель клиента, может быть пустой в случае создания
     * @return Client $client
     */
    public function findOrCreate(Trafic $trafic) :Client
    {
        $query = Client::select('clients.*')->with('phones');

        if($trafic->client_type_id == 1)
            $query->leftJoin('client_phones','client_phones.client_id','clients.id')
                ->where('client_phones.phone', $trafic->phone);

        elseif($trafic->client_type_id == 2 || $trafic->client_type_id == 3)
            $query->leftJoin('client_inns','client_inns.client_id','clients.id')
                ->where('client_inns.number', $trafic->inn);

        $client = $query->first();

        if(!$client)
            $client = Client::create([
                'lastname'      => $trafic->lastname,
                'firstname'     => $trafic->firstname,
                'fathername'    => $trafic->fathername,
                'client_type_id' => $trafic->client_type_id,
                'trafic_sex_id'  => $trafic->trafic_sex_id,
                'trafic_zone_id'  => $trafic->trafic_zone_id,
                'company_name'  => $trafic->company_name,
            ]);

        if($client->wasRecentlyCreated) {
            if($trafic->client_type_id == 1) {
                $client->phones()->create([
                    'phone' => $trafic->phone
                ]);
                $client->emails()->create([
                    'email' => $trafic->email
                ]);
            }
            if($trafic->client_type_id == 2)
            {
                $client->inn()->create([
                    'number' => $trafic->inn
                ]);
            }
        }

        return $client;
    }

    public static function getClientFromTrafic(Trafic $trafic)
    {
        $me = new self;
        return $me->findOrCreate($trafic);
    }

    /**
     * Удалить мягко клиента
     * @param Client $client Client
     * @return void
     */
    public function delete(Client $client) :void
    {
        if($client->client_type_id == 2)
            throw new \Exception('Нельзя удалять юр.лицо');

        $client->phones()->delete();
        $client->delete();
    }

    /**
     * Метод возращает количество клиентов, удовлетворяющих условию фильтра
     * @param array $data данные для фильтра
     * @return int $result int
     */
    public function counter($data = []) : int
    {
        $query = Client::select(\DB::raw('count(clients.id)'));
        $filter = app()->make(ClientFilter::class, ['queryParams' => array_filter($data)]);
        $query->filter($filter)
            ->groupBy('clients.id');
        $result = $query->get()->count();
        return $result;
    }
}
