<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Marker;

class fillMarker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:fillmarker';

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
        $data = ['Блок','План', 'На обмен', 'Приоритет'];
        foreach($data as $item) {
            $obj = new Marker();
            $obj->name = $item;
            $obj->save();
        }
    }
}
