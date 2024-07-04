<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SQL_VIEW_TRAFIC_PRODUCT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:traficprod';

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
        $query = "CREATE OR REPLACE view trafic_products as
        SELECT
                --     concat('model',m.id) as number,
                --     m.id as uid,
                --     'App\\Models\\Marks' as model,
                --     m.name as name,
                --     12 as appeal_id,
                --     cb.company_id,
                --     0 as duration,
                --     NULL /*if(min(complectations.price)is null, 0, min(complectations.price))*/ as price,
                --     NULL as description,
                --     NULL as group_id
                -- FROM marks m
                --     LEFT JOIN brands b on m.brand_id = b.id
                --     LEFT JOIN company_brands cb on b.id = cb.brand_id
                --     /*LEFT JOIN complectations on m.id = complectations.mark_id*/
                --    WHERE cb.brand_id IS NOT NULL
                --    AND m.status > 0 AND diller_status = 1
                -- GROUP BY m.id,cb.company_id

                -- UNION SELECT
                    concat ('service',service_products.id) as number,
                    service_products.id as uid,
                    'App\\Models\\ServiceProduct' as model,
                    name,
                    appeal_id,
                    company_id,
                    duration,
                    price,
                    description,
                    group_id
                FROM service_products

                UNION SELECT
                        concat('alias',ma.id) as number,
                        ma.id as uid,
                        'App\\Models\\MarkAliases' as model,
                        ma.name as name,
                        12 as appeal_id,
                        cb.company_id as company_id,
                        0 as duration,
                        if(min(curcp.price) is null, 0, min(curcp.price)) as price,
                        NULL as description,
                        NULL as group_id

                    FROM complectation_mark_aliases as cma

                    RIGHT JOIN mark_aliases as ma on ma.id = cma.mark_alias_id

                    LEFT JOIN complectations as c on c.id = cma.complectation_id

                    LEFT JOIN complectation_current_prices as curcp on curcp.complectation_id = c.id

                    LEFT JOIN marks as m on m.id = c.mark_id

                    LEFT JOIN company_brands as cb on cb.brand_id = m.brand_id

                    GROUP BY ma.id, cb.company_id";

        \DB::statement($query);
    }
}
