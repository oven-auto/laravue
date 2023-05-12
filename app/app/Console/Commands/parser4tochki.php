<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class parser4tochki extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:car';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Пропарсить 4tochki.ru на марки модели и тд';

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

    }
}
