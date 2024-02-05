<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CMExpert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SMExpertApi';

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
        $this->call('cm:bodies');
        $this->call('cm:gears');
        $this->call('cm:drivers');
        $this->call('cm:engines');
        $this->call('cm:colors');
    }
}
