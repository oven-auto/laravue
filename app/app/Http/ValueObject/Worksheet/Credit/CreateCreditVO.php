<?php

namespace App\Http\ValueObject\Worksheet\Credit;

Class CreateCreditVO
{
    public function __construct(
        public readonly int|null $worksheet_id,
        public readonly int|null $debtor_id,
        public readonly int|null $calculation_type,
        public readonly int|null $creditor_id,
        public readonly int|null $status_id,
        public readonly int|null $author_id,
        public readonly bool|null $close,
    )
    {
        
    }



    public static function fromArray(array $data)
    {
        return new self(
            worksheet_id            : $data['worksheet'] ?? null,  
            debtor_id               : $data['debtor'] ?? null,  
            calculation_type        : $data['tactic'] ?? null,  
            creditor_id             : $data['creditor'] ?? null,  
            status_id               : $data['status'] ?? null,  
            author_id               : $data['author'] ?? null,  
            close                   : $data['close'] ?? null,  
        );
    }
}