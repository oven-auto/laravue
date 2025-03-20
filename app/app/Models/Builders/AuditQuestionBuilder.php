<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

Class AuditQuestionBuilder extends Builder
{
    public function __construct(QueryBuilder $builder)
    {
        parent::__construct($builder);
        
        $this->addSelect([
            DB::raw('audit_questions.*'),
            DB::raw('sum(audit_questions.weigth) OVER (PARTITION BY audit_questions.audit_id) as physic_weigth'),
            DB::raw('count(audit_questions.id) OVER (PARTITION BY audit_questions.audit_id) as count_question'),
            DB::raw('SUM(IF(audit_questions.weigth IS NOT NULL, 1, 0)) OVER (PARTITION BY audit_questions.audit_id) as count_out_weigth'),
        ]);
    }



    public function select(array|string $data)
    {
        return $this->addSelect($data);
    }
}