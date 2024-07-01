<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;

class CarCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment'           => 'sometimes|string',
            //car options
            'options'           => 'sometimes|array',
            'options.*'         => 'numeric',

            //TAB I (ОСНОВНОЕ)
            'mark_id'           => 'required|numeric',
            'brand_id'          => 'required|numeric',
            'complectation_id'  => 'required|numeric',
            'color_id'          => 'required|numeric',
            'year'              => 'required|numeric',
            'vin'               => 'sometimes|string',
            'order_number'      => 'sometimes|string',
            'marker_id'         => 'sometimes|numeric',
            'trade_marker_id'   => 'required|numeric',
            'provider_id'       => 'required|numeric',

            //TAB II (ЛОГИСТИКА)
            'order_type_id'     => 'sometimes|numeric',
            'order_date'        => 'sometimes|date_format:d.m.Y',
            'plan_date'         => 'sometimes|date_format:d.m.Y',
            'build_date'        => 'sometimes|date_format:d.m.Y',
            'ready_date'        => 'sometimes|date_format:d.m.Y',
            'request_date'      => 'sometimes|date_format:d.m.Y',
            'shipment_date'     => 'sometimes|date_format:d.m.Y',
            'stock_date'        => 'sometimes|date_format:d.m.Y',
            'technic_id'        => 'sometimes|numeric',
            'presale_date'      => 'sometimes|date_format:d.m.Y',
            'audio_code'        => 'sometimes|string',

            //TAB III (Складской учет)
            'invoice_date'      => 'sometimes|date_format:d.m.Y',
            'ransom_date'       => 'sometimes|date_format:d.m.Y',
            'off_date'          => 'sometimes|date_format:d.m.Y',
            'purchase_cost'     => 'sometimes|numeric',

            //delivery terms
            'delivery_term_id'    => 'sometimes',

            //Detailing cost
            'detailing_costs'                       => 'sometimes|array',
            'detailing_costs.*.detailing_cost_id'   => 'numeric|nullable',
            'detailing_costs.*.price'               => 'numeric|nullable',
            'detailing_costs.*.coefficient'         => 'numeric|nullable',

            //TUNING
            'tuning'            => 'sometimes|array',
            'tuning.price'      => 'nullable|numeric',
            'tuning.devices'    => 'array',
            'tuning.devices.*'  => 'numeric',

            'disable_sale'      => 'sometimes|boolean',
            'disable_off'       => 'sometimes|boolean',

            'collector_id'      => 'sometimes'
        ];
    }



    public function messages()
    {
        return [
            'mark_id.required'              => 'Поле модель обязательно',
            'brand_id.required'             => 'Поле бренд обязательно',
            'complectation_id.required'     => 'Поле комплектация обязательно',
            'color_id.required'             => 'Поле цвет обязательно',
            'year.required'                 => 'Поле год выпуска обязательно',
            'vin.required'                  => 'Поле VIN обязательно',
            'order_number.required'         => 'Поле номер заказа обязательно',
            'trade_marker_id.required'      => 'Поле товарный признак обязательно',
            'provider_id.required'          => 'Поле поставщик обязательно',
        ];
    }
}
