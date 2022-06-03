<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliveryStage;

class fillDeliveryStage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:filldeliverystage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = [
            [
                'name'=>'В наличии',
                'description' => 'Этот автомобиля ожидает Вас на нашем складе. Кликните по автомобилю и перейдите на страницу предложения, вероятно, она уже дополнена живыми фотографиями.'
            ],
            [
                'name' => 'В отгрузке',
                'description' => 'В ближайшее время этот автомобиль поступит на склад. Он уже едет к нам на автовозе или ожидает погрузку на складе завода. Срок поставки совсем небольшой.'
            ],
            [
                'name' => 'В производстве',
                'description' => 'Этот автомобиль запланирован в производство на заводе. Это означает, что он еще не готов, но дата сборки уже известна. Кстати, иногда еще не поздно поменять параметры машины (например, цвет или опции), для этого кликните по автомобилю и нажмите кнопку «Изменить опции».'
            ]
        ];

        foreach($data as $item) {
            $obj = new DeliveryStage();
            $obj->name = $item['name'];
            $obj->description = $item['description'];
            $obj->save();
        }
    }
}
