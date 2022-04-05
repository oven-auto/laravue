<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliveryType;
use DB;

class FillDeliveryTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:filldeliverytypes';

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
        $data = ['Товарный','Тест-драйв', 'Подменный', 'С пробегом'];
        foreach($data as $item) {
            $obj = new DeliveryType();
            $obj->name = $item;
            $obj->save();
        }
    }
}
