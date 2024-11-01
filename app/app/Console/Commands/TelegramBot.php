<?php

namespace App\Console\Commands;

use App\Models\Banner;
use Illuminate\Console\Command;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgbot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить телеграм бота';

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
    public function handle(\App\Classes\Telegram\Scenario $scenario)
    {
        $scenario->handler();
    }
}
