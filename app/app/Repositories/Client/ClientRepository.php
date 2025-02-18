<?php

namespace App\Repositories\Client;

use App\Helpers\Date\DateHelper;
use App\Models\Client;
use App\Http\Filters\ClientFilter;
use App\Models\Trafic;
use Illuminate\Support\Arr;
use App\Models\ClientPassport;
use Illuminate\Support\Facades\DB;

/**
 * Репазиторий модели Client
 */
class ClientRepository
{
    /**
     * Метод задает запрос на получение списка клиентов удовлетворяющих заданным свойствам фильтра
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Builder $query \Illuminate\Database\Eloquent\Builder
     */
    private function filter($data = []): \Illuminate\Database\Eloquent\Builder
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
    public function paginate($data = [], $paginate = 10): \Illuminate\Contracts\Pagination\Paginator
    {
        $query = $this->filter($data);
        $query->with(['latest_worksheet', 'phones', 'emails', 'cars', 'inn', 'zone', 'sex',]);
        $query->groupBy('clients.id');
        $result = $query->simplePaginate($paginate);
        return $result;
    }



    /**
     * Метод возращает постраничную коллекию клиентов, прошедших фильтрацию
     * @param array $data данные для фильтра
     * @param integer $paginate не обязательное поле, по умолчанию 10
     * @return \Illuminate\Contracts\Pagination\Paginator $result \Illuminate\Contracts\Pagination\Paginator
     */
    public function get($data = [], $length = 10)
    {
        $query = $this->filter($data);
        $query->with(['latest_worksheet', 'phones', 'emails', 'cars', 'inn', 'zone', 'sex',]);
        $query->groupBy('clients.id');
        $result = $query->limit($length)->get();
        return $result;
    }



    /**
     * Метод возращает коллекию клиентов (нет постраничного вывода!!!), прошедших фильтрацию
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection $result \Illuminate\Database\Eloquent\Collection
     */
    public function export($data = []): \Illuminate\Database\Eloquent\Collection
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
    public function save(Client $client, $data = []): Client
    {
        $client->fill(Arr::only($data, Arr::except(Client::getColumnsName(), ['id'])))->save();

        $this->savePassport($client, $data);

        if($client->isCompany())
            if (isset($data['inn']))
                $this->saveInn($client, $data['inn']);
        
        $client->phones()->delete();
        
        $client->emails()->delete();

        foreach($data['phones'] as $item)
            if(!$client->phones->contains('phone', $item['phone']))
                $this->savePhone($client, $item['phone'], $item['empty_phone']);

        if(isset($data['emails']))
            foreach($data['emails'] as $item)
                if(!$client->emails->contains('email', $item))
                    $this->saveEmail($client, $item);

        // foreach ($data['contacts'] as $itemRowContact) {
        //     if (isset($itemRowContact['phone']))
        //         $this->savePhone($client, $itemRowContact['phone']);

        //     if (isset($itemRowContact['email']))
        //         $this->saveEmail($client, $itemRowContact['email']);
        // }

        $client->refresh();

        return $client;
    }



    public function saveInn(Client $client, string $inn)
    {
        $client->inn->fill([
            'number' => $inn
        ])->save();
    }



    private function savePhone(Client $client, string $phone, bool $empty)
    {
        $phone = preg_replace("/[^,.0-9]/", '', $phone);

        if(!$client->phones->contains('phone', $phone))
            $client->phones()->create([
                'client_id' => $client->id, 
                'phone' => $phone,
                'empty_phone' => $empty
            ]);
    }



    private function saveEmail(Client $client, string $email)
    {
        if(!$client->emails->contains('email', $email))
            $client->emails()->create([
                'client_id' => $client->id, 
                'email' => $email
            ]);
    }



    public function savePassport(Client $client, array $data)
    {
        $passportData = Arr::only($data, ClientPassport::getColumnsName());
        $passportData['birthday_at'] =              DateHelper::getFormatedDate($passportData['birthday_at'] ?? null, 'd.m.Y', 'Y-m-d');
        $passportData['driver_license_issue_at'] =  DateHelper::getFormatedDate($passportData['driver_license_issue_at'] ?? null, 'd.m.Y', 'Y-m-d');
        $passportData['passport_issue_at'] =        DateHelper::getFormatedDate($passportData['passport_issue_at'] ?? null, 'd.m.Y', 'Y-m-d');
        $passportData['client_id'] = $client->id;
        $passportData['form_owner_id'] = isset($passportData['form_owner_id']) ? $passportData['form_owner_id'] : null;
        $client->passport->fill($passportData)->save();
    }

    

    /**
     * Метод создания или получения клиента из бд, используется при создании рабочего листа Worksheet из трафика Trafic
     * @param Trafic $trafic содержит модель клиента, может быть пустой в случае создания
     * @return Client $client
     */
    public function findOrCreate(Trafic $trafic): Client
    {
        $query = Client::select('clients.*')->with('phones');
       
        if ($trafic->client->client_type_id == 1)
            $query->leftJoin('client_phones', 'client_phones.client_id', 'clients.id')
                ->where('client_phones.phone', $trafic->client->phone);
        
        elseif ($trafic->client->client_type_id == 2 || $trafic->client->client_type_id == 3)
            $query->leftJoin('client_inns', 'client_inns.client_id', 'clients.id')
                ->where('client_inns.number', $trafic->client->inn);
        
        $client = $query->first();

        if (!$client)
            $client = Client::create([
                'lastname'          => $trafic->client->lastname,
                'firstname'         => $trafic->client->firstname,
                'fathername'        => $trafic->client->fathername,
                'client_type_id'    => $trafic->client->client_type_id,
                'trafic_sex_id'     => $trafic->client->trafic_sex_id,
                'trafic_zone_id'    => $trafic->trafic_zone_id,
                'company_name'      => $trafic->client->company_name,
            ]);
            
        if ($client->wasRecentlyCreated) {
            if ($trafic->client->client_type_id == 1) {
                $client->phones()->create([
                    'phone' => $trafic->client->phone,
                    'empty_phone' => $trafic->client->empty_phone,
                ]);
                $client->emails()->create([
                    'email' => $trafic->client->email
                ]);
            }
            if ($trafic->client->client_type_id == 2) {
                $client->inn()->create([
                    'number' => $trafic->client->inn
                ]);
            }
        }

        $client->save();

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
    public function delete(Client $client): void
    {
        if ($client->client_type_id == 2)
            throw new \Exception('Нельзя удалять юр.лицо');

        $client->phones()->delete();
        $client->delete();
    }

    /**
     * Метод возращает количество клиентов, удовлетворяющих условию фильтра
     * @param array $data данные для фильтра
     * @return int $result int
     */
    public function counter($data = []): int
    {
        $query = Client::select(DB::raw('count(clients.id)'));
        $filter = app()->make(ClientFilter::class, ['queryParams' => array_filter($data)]);
        $query->filter($filter)
            ->groupBy('clients.id');
        $result = $query->get()->count();
        return $result;
    }
}
