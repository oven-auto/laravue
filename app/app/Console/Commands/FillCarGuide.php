<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FillCarGuide extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car:guide';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнить все вспомогательные справочники для модели CAR';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logisticState = [
            ['system_name' => 'order_date',     'state' => 1,   'name' => 'Заказ'],
            ['system_name' => 'plan_date',      'state' => 2,   'name' => 'Сборка планируемая'],
            ['system_name' => 'build_date',     'state' => 3,   'name' => 'Сборка фактическая'],
            ['system_name' => 'ready_date',     'state' => 4,   'name' => 'Готовность к отгрузке'],
            ['system_name' => 'request_date',   'state' => 5,   'name' => 'Заявка на перевозку'],
            ['system_name' => 'shipment_date',  'state' => 6,   'name' => 'Отгрузка'],
            ['system_name' => 'stock_date',     'state' => 7,   'name' => 'Приемка на склад'],
            ['system_name' => 'presale_date',   'state' => 0,   'name' => 'Предпродажка'],
            ['system_name' => 'invoice_date',   'state' => 8,   'name' => 'Приходная накладная'],
            ['system_name' => 'ransom_date',    'state' => 0,   'name' => 'Оплата поставщику'],
            ['system_name' => 'sale_date',      'state' => -1,  'name' => 'Продажа'],
            ['system_name' => 'off_date',       'state' => 9,   'name' => 'Списание'],   
        ];

        foreach($logisticState as $item){
            \App\Models\LogisticState::updateOrCreate(
                ['system_name' => $item['system_name']],
                $item
            );
        }

        ///////////////////////////////////////////////

        $terms = [
            ['name' => 'Предоплата',    'sort' => 1],
            ['name' => 'Факторинг',     'sort' => 2],
            ['name' => 'Консигнация',   'sort' => 3],
            ['name' => 'Гарантия',      'sort' => 4],
            ['name' => 'Акредитив',     'sort' => 5],
        ];

        foreach($terms as $item)
            \App\Models\DeliveryTerm::updateOrCreate(
                ['name' => $item['name']],
                $item
            );

        /////////////////////////////////////////////

        $orderTypes = [
            ['name' => 'Квота'],
            ['name' => 'Перевод'],
            ['name' => 'Обмен'],
            ['name' => 'Поиск'],
            ['name' => 'Возврат'],
        ];

        foreach($orderTypes as $item)
            \App\Models\OrderType::updateOrCreate(
                ['name' => $item['name']],
                $item
            );

        ////////////////////////////////////////////////////

        $tradeMarks = [
            ['name' => 'Товарный автомобиль'],
            ['name' => 'Подменный автомобиль'],
            ['name' => 'Демо автомобиль'],
            ['name' => 'Возврат'],
        ];

        foreach($tradeMarks as $item)
            \App\Models\TradeMarker::updateOrCreate(
                ['name' => $item['name']],
                $item
            );

        //////////////////////////////////////////////////

        $detailingCosts = [
            ['name' => 'Доставка'],
        ];

        foreach($detailingCosts as $item)
            \App\Models\DetailingCost::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
    }
}
