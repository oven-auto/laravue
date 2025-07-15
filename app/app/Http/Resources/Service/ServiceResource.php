<?php

namespace App\Http\Resources\Service;

use App\Http\Resources\Services\ServiceCategoryResource;
use App\Http\Resources\User\UserSmallResource;
use App\Http\Resources\Worksheet\Reserve\ReserveList\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'category'          => new ServiceCategoryResource($this->category),
            'author'            => new UserSmallResource($this->author),
            'provider'          => new ClientResource($this->provider),
            'updated_at'        => $this->updated_at->format('d.m.Y'),
            'applicability'     => $this->applicabilities,
            'name'              => $this->name,
            'cost'              => $this->calculation->cost             ?? 0,
            'company_award'     => $this->calculation->company_award    ?? 0,
            'design_award'      => $this->calculation->design_award     ?? 0,
            'sale_award'        => $this->calculation->sale_award       ?? 0,
            'reminder'          => $this->prolongation->reminder        ?? 0,
            'manager'           => $this->prolongation->manager_id      ?? 0,
        ];
    }
}
