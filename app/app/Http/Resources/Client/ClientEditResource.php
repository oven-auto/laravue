<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $contacts = [];

        foreach($this->phones as $key => $item)
            $contacts[$key]['phone'] = $item->phone_mask;

        foreach($this->emails as $key => $item)
            $contacts[$key]['email'] = $item->email;

        if($this->id)
            return [
                'data' => [
                    'id'                        => $this->id,
                    'firstname'                 => $this->firstname ? $this->firstname : null,
                    'lastname'                  => $this->lastname ? $this->lastname : null,
                    'fathername'                => $this->fathername ? $this->fathername : null,
                    'client_type_id'            => $this->client_type_id,
                    'trafic_sex_id'             => $this->trafic_sex_id,
                    'trafic_zone_id'            => $this->zone->id ? $this->zone : null,
                    'birthday_at'               => ($this->passport->birthday_at) ? $this->passport->birthday_at->format('d.m.Y H:i') : null,
                    'address'                   => $this->passport->address,
                    'driver_license_issue_at'   => $this->passport->driver_license_issue_at ? $this->passport->driver_license_issue_at->format('d.m.Y H:i') : null,
                    'driving_license'           => $this->passport->driving_license,
                    'passport_issue_at'         => $this->passport->passport_issue_at ? $this->passport->passport_issue_at->format('d.m.Y H:i') : null,
                    'serial_number'             => $this->passport->serial_number,
                    'contacts'                  => $contacts
                ],
                'success' => 1,
            ];
        else
            return [
                'success' => 0,
                'message' => 'Такой Клиент не существует'
            ];
    }
}
