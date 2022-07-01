<?php

namespace App\Http\Resources\Motor;

use Illuminate\Http\Resources\Json\JsonResource;

class MotorListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'motor_type_id' => $this->motor_type_id,
            'motor_transmission_id' => $this->motor_transmission_id,
            'motor_driver_id' => $this->motor_driver_id,
            'name' => $this->name,
            'power' => $this->power,
            'valve' => $this->valve,
            'size' => $this->size,
            'motor_toxic_id' => $this->motor_toxic_id,
            'transmission_name' => $this->transmission_name,
            'brand' => $this->brand,
            'transmission'  => $this->transmission,
            'type'  => $this->type,
            'driver'  => $this->driver,
            'toxic'  => $this->toxic,
        ];
    }
}
