<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTraficProductsView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traficview:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создаст представление для отображения всех моделей, продуктов и услуг для трафика';

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
        $query = "CREATE view trafic_products as
            SELECT concat('model',m.id) as id, m.name, 1 as appeal_id, cb.company_id
                from marks m
                    left join brands b on m.brand_id = b.id
                    LEFT JOIN company_brands cb on b.id = cb.brand_id
            UNION SELECT concat('service',id) as id, name, appeal_id, company_id from service_products sp ";

        \DB::statement($query);
    }
}
