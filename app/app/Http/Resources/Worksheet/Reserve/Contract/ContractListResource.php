<?php

namespace App\Http\Resources\Worksheet\Reserve\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'worksheet' => [
                'id' => $this->reserve->worksheet->id,
            ],
            'car' => [
                'id' => $this->reserve->car->id,
                'vin' => $this->reserve->car->vin,
                'brand' => $this->reserve->car->brand->name,
                'mark' => $this->reserve->car->mark->name,
            ],
            'client' => [
                'id' => $this->reserve->worksheet->client->id,
                'name' => $this->reserve->worksheet->client->full_name,
                'zone' => $this->reserve->worksheet->client->zone->name,
            ],
            'dkp' => [
                'offer_at' => $this->dkpOfferDate,
                'author' => $this->dkp_decorator->cut_name,
                'status' => $this->getDKPStatusString(),
            ],
            'pdkp' => [
                'offer_at' => $this->pdkpOfferDate,
                'author' => $this->pdkp_decorator->cut_name,
                'status' => $this->getPDKPStatusString(),
            ],
            'arrears' => [
                'debit' => $this->getDebtorArrears(),
                'credit' => $this->getCreditorArrears(),
            ],
        ];
    }
}
