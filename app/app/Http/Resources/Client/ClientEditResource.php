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
        if($this->id)
            return [
                'data' => [
                    'id'                        => $this->id,
                    'firstname'                 => $this->firstname,
                    'lastname'                  => $this->lastname,
                    'fathername'                => $this->fathername,
                    'client_type_id'            => $this->client_type_id,
                    'trafic_sex_id'             => $this->trafic_sex_id,
                    'trafic_zone_id'            => $this->trafic_zone_id,
                    'birthday_at'               => ($this->passport->birthday_at) ? $this->passport->birthday_at->format('d.m.Y H:i') : '',
                    'address'                   => $this->passport->address,
                    'driver_license_issue_at'   => $this->passport->driver_license_issue_at ? $this->passport->driver_license_issue_at->format('d.m.Y H:i') : '',
                    'driving_license'           => $this->passport->driving_license,
                    'passport_issue_at'         => $this->passport->passport_issue_at ? $this->passport->passport_issue_at->format('d.m.Y H:i') : '',
                    'serial_number'             => $this->passport->serial_number
                ],
                'success' => 1,
                'message' => $this->isCreate() ? 'Клиент добавлен' : 'Клиент изменен'
            ];
        else
            return [
                'success' => 0,
                'message' => 'Такой Клиент не существует'
            ];
    }
}
