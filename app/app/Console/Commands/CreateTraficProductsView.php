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
        SELECT
                    concat('model',m.id) as number,
                    m.id as uid,
                    'App\\Models\\Marks' as model,
                    m.name as name,
                    12 as appeal_id,
                    cb.company_id,
                    0 as duration,
                    if(min(complectations.price)is null, 0, min(complectations.price)) as price
                FROM marks m
                    LEFT JOIN brands b on m.brand_id = b.id
                    LEFT JOIN company_brands cb on b.id = cb.brand_id
                    LEFT JOIN complectations on m.id = complectations.mark_id
                   WHERE cb.brand_id IS NOT NULL
                GROUP BY m.id,cb.company_id

                UNION SELECT
                    concat ('service',service_products.id) as number,
                    service_products.id as uid,
                    'App\\Models\\ServiceProduct' as model,
                    name,
                    appeal_id,
                    company_id,
                    duration,
                    price
                FROM service_products";

        \DB::statement($query);
    }
}


