<?php

namespace App\Repositories\Worksheet\Modules\Redemption;

use App\Http\Filters\WSMRedemptionCarFilter;
use App\Models\RedemptionStatus;
use App\Models\UsedCar;
use App\Models\Worksheet;
use App\Models\WSMRedemptionCar;
use App\Models\WSMRedemptionLink;
use App\Services\GetShortCutFromURL\GetShortCutFromURL;
use Illuminate\Support\Arr;
use \App\Services\Comment\Comment;
use Illuminate\Support\Facades\Http;

/**
 * РЕПОЗИТОРИЙ ОЦЕНОК
 * - ПОЛУЧИТЬ ВСЕ ОЦЕНКИ ИЗ РАБОЧЕГО ЛИСТА
 * - ПОЛУЧИТЬ СПИСОК ОЦЕНОК ВВИДЕ ПАГИНАЦИИ, ПО ОПРЕДЕЛЕННЫМ ПАРАМЕТРАМ
 * - ПОЛУЧИТЬ КОЛ-ВО ОЦЕНОК, ПО ОПРЕДЕЛЕННЫМ ПАРАМЕТРАМ
 * - СОЗДАТЬ ОЦЕНКУ АВТОМОБИЛЯ В РАМАХ РЛ
 * - ИЗМЕНИТЬ ОЦЕНКУ
 * - ПЕРЕВЕСТИ МАШИНУ НА СКЛАД
 * - СОХРАНИТЬ СВОДНЫЕ ДАННЫЕ ОЦЕНКИ
 * - ПРОВЕРИТЬ МАШИНУ , ЧТО ОНА НЕ СОДЕРЖИТСЯ В ОЦЕНКЕ В РАМКАХ ОДНОГО РЛ
 * - СОХРАНИТЬ ССЫЛКУ В ОЦЕНКУ
 * - ЗАВЕРШИТЬ ОЦЕНКУ
 *
 * 16-01-2024
 *
 * - СПИСОК ВСЕХ КОММЕНТАРИЕВ
 * - ДОБАВИТЬ КОММЕНТАРИЙ
 * - ВЕРНУТЬ ОЦЕНКУ ЗАКРЫТУЮ (НЕ ВЫКУПЛЕННУЮ) В РАБОТУ
 */
Class RedemptionRepository
{
    /**
     * ПОЛУЧИТЬ ВСЕ ОЦЕНКИ ИЗ РАБОЧЕГО ЛИСТА
     * @param int $worksheet
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(int $worksheet = 0) : \Illuminate\Database\Eloquent\Collection
    {
        $query = WSMRedemptionCar::select('wsm_redemption_cars.*');

        $query->with([
            'offers', 'calculations', 'purchases', 'status', 'author',
            'client_car', 'car_sale_sign', 'links', 'status'
        ]);

        if($worksheet)
            $query->where('worksheet_id', $worksheet);

        $redemptions = $query->get();

        return $redemptions;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ОЦЕНОК ВВИДЕ ПАГИНАЦИИ, ПО ОПРЕДЕЛЕННЫМ ПАРАМЕТРАМ
     * @param array $data
     * @param int $paginate
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate(array $data, $paginate = 20) : \Illuminate\Contracts\Pagination\Paginator
    {
        $query = WSMRedemptionCar::select('wsm_redemption_cars.*');

        $filter = app()->make(WSMRedemptionCarFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $query->with([
            'final_author','worksheet', 'last_offer', 'last_calculation', 'last_purchase',
            'status', 'author', 'client_car', 'car_sale_sign', 'links', 'status', 'client'
        ]);

        $result = $query->simplePaginate($paginate);

        return $result;
    }



    /**
     * ПОЛУЧИТЬ КОЛ-ВО ОЦЕНОК, ПО ОПРЕДЕЛЕННЫМ ПАРАМЕТРАМ
     * @param array $data
     * @return int
     */
    public function count(array $data) : int
    {
        $subQuery = WSMRedemptionCar::select('wsm_redemption_cars.id');

        $filter = app()->make(WSMRedemptionCarFilter::class, ['queryParams' => array_filter($data)]);

        $subQuery->filter($filter);

        $res = \DB::query()->fromSub($subQuery, 'subQ')->count();

        return $res;
    }



    /**
     * СОЗДАТЬ ОЦЕНКУ АВТОМОБИЛЯ В РАМАХ РЛ
     * @param Worksheet $worksheet
     * @param array $data
     * @return WSMRedemptionCar
     */
    public function store(Worksheet $worksheet, array $data) : WSMRedemptionCar
    {
        $obj = (object) $data;

        $this->check_car($worksheet, $obj->client_car_id);

        $redemption = $worksheet->redemptions()->create([
            'client_car_id' => $obj->client_car_id,
            'worksheet_id' => $worksheet->id,
            'car_sale_sign_id' => $obj->car_sale_sign_id,
            'author_id' => auth()->user()->id,
            'client_id' => $worksheet->client_id,
            'redemption_status_id' => 1,
            'expectation' => $obj->expectation ?? 0,
            'redemption_type_id' => $obj->redemption_type_id,
        ]);

        $this->save($redemption, $data);

        Comment::add($redemption, 'create');

        return $redemption;
    }



    /**
     * ИЗМЕНИТЬ ОЦЕНКУ
     * @param WSMRedemptionCar $redemption
     * @param array $data
     * @return WSMRedemptionCar
     */
    public function update(WSMRedemptionCar $redemption, array $data) : WSMRedemptionCar
    {
        $obj = (object) $data;

        $redemption->fill([
            'expectation' => $obj->expectation ?? 0,
            'car_sale_sign_id' => $obj->car_sale_sign_id ?? $redemption->car_sale_sign_id,
            'redemption_type_id' => $obj->redemption_type_id ?? $redemption->redemption_type_id,
        ])->save();

        $this->save($redemption, $data);

        return $redemption;
    }



    /**
     * ПЕРЕВЕСТИ МАШИНУ НА СКЛАД
     * @param WSMRedemptionCar $redemption
     * @return void
     */
    public function buyCar(WSMRedemptionCar $redemption) : void
    {
        if(!$redemption->last_purchase->price)
            throw new \Exception('Фактический закуп не заполнен, не могу перенести такой автомобиль на склад');
        if($redemption->redemption_status_id != 1)
            throw new \Exception('Эта оценка не является рабочей');
        if(!$redemption->client_car->vin)
            throw new \Exception('Нельзя создать на складе автомобиль без VIN-номера');

        $redemption->redemption_status_id = RedemptionStatus::where('slug', 'stock')->first()->id;
        $redemption->save();
        $redemption->final_author()->updateOrCreate(['author_id' => auth()->user()->id]);

        $redemption->client_car->fill(['actual' => 0])->save();

        $carData = $redemption->client_car->getAttributes();
        $carData['agent_id'] = $redemption->client_id;
        $carData['author_id'] = auth()->user()->id;
        $carData['wsm_redemption_car_id'] = $redemption->id;
        $carData['purchase_price'] = $redemption->last_purchase->price;
        UsedCar::create(Arr::except($carData, ['created_at','updated_at','client_id','actual','editor_id']));

        Comment::add($redemption, 'buy');
    }



    /**
     * СОХРАНИТЬ СВОДНЫЕ ДАННЫЕ ОЦЕНКИ (РАСЧЕТНАЯ ЦЕНА, ПРЕДЛОЖЕНИЕ, ФАКТ)
     * @param WSMRedemptionCar $redemption
     * @param array $data
     * @return void
     */
    public function save(WSMRedemptionCar $redemption, array $data) : void
    {
        if(isset($data['offer']))
        {
            $redemption->offers()->create([
                'author_id' => auth()->user()->id,
                'wsm_redemption_car_id' => $redemption->id,
                'price' => $data['offer'],
            ]);

            //Comment::add($redemption, 'offer');
        }

        if(isset($data['price_begin']) || isset($data['price_end']))
        {
            $redemption->calculations()->create([
                'author_id' => auth()->user()->id,
                'wsm_redemption_car_id' => $redemption->id,
                'price_begin' => $data['price_begin'] ?? 0,
                'price_end' => $data['price_end'] ?? 0,
            ]);

            //Comment::add($redemption, 'calculation');
        }

        if(isset($data['purchase']))
        {
            $redemption->purchases()->create([
                'author_id' => auth()->user()->id,
                'wsm_redemption_car_id' => $redemption->id,
                'price' => $data['purchase'],
            ]);

            //Comment::add($redemption, 'purchase');
        }
    }



    /**
     * ПРОВЕРИТЬ МАШИНУ , ЧТО ОНА НЕ СОДЕРЖИТСЯ В ОЦЕНКЕ В РАМКАХ ОДНОГО РЛ
     * @param Worksheet $worksheet ID Рабочего листа
     * @param int $car_id ID машины клиента
     * @return void
     */
    private function check_car(Worksheet $worksheet, int $car_id) : void
    {
        $check = WSMRedemptionCar::where('worksheet_id', $worksheet->id)
            ->where('client_car_id', $car_id)
            ->first();

        if($check)
            throw new \Exception('Оценка этого автомобиля уже проводится в рамках данного рабочего листа');
    }



    /**
     * СОХРАНИТЬ ССЫЛКУ В ОЦЕНКУ
     * @param WSMRedemptionCar $redemption
     * @param array $data
     * @return WSMRedemptionLink
     */
    public function saveLink(WSMRedemptionCar $redemption, array $data) : WSMRedemptionLink
    {
        $link = WSMRedemptionLink::create([
            'author_id' => auth()->user()->id,
            'icon' => GetShortCutFromURL::get($data['url']),
            'url' => $data['url'],
            'wsm_redemption_car_id' => $redemption->id,
        ]);

        return $link;
    }



    /**
     * ЗАВЕРШИТЬ ОЦЕНКУ
     * @param WSMRedemptionCar $redemption
     * @return void
     */
    public function close(WSMRedemptionCar $redemption) : void
    {
        if($redemption->redemption_status_id == 1)
        {
            $redemption->fill([
                'redemption_status_id' => 3,
            ])->save();

            $redemption->final_author()->create(['author_id' => auth()->user()->id]);

            Comment::add($redemption, 'close');
        }

        else
            throw new \Exception('Эта оценка не является рабочей');
    }



    /**
     *  СПИСОК ВСЕХ КОММЕНТАРИЕВ ОЦЕНКИ
     */
    public function getComments(WSMRedemptionCar $redemption)
    {
        return $redemption->comments->map(fn($item) => [
            'text' => $item->text,
            'id' => $item->id,
            'author' => $item->author->cut_name,
            'created_at' => $item->created_at->format('d.m.Y (H:i)'),
        ]);
    }



    /**
     * ДОБАВИТЬ КОММЕНТАРИЙ
     */
    public function addComment(WSMRedemptionCar $redemption, string $text)
    {
        $redemption->comments()->create([
            'text' => $text,
            'author_id' => auth()->user()->id,
        ]);
    }



    /**
     * ВЕРНУТЬ ОЦЕНКУ ЗАКРЫТУЮ (НЕ ВЫКУПЛЕННУЮ) В РАБОТУ
     */
    public function revert(WSMRedemptionCar $redemption)
    {
        if($redemption->status->slug !== 'closed')
            throw new \Exception('Оценка не завершена');

        $redemption->redemption_status_id = 1;
        $redemption->final_author()->delete();
        $redemption->save();
        $redemption->load('status');
    }
}
