<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SQL_VIEW_FULL_COMMAND extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:view';

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


        $this->call('sql:optioncurrentprice');
        $this->call('sql:optionprice');

        $this->call('sql:comprice');
        $this->call('sql:carfullprice');

        $this->call('sql:contractprice');
        $this->call('sql:contractoptionprice');

        $this->call('sql:traficprod');
    }
}
