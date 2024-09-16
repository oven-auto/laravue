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
            'comment'           => 'sometimes|string|nullable',
            //car options
            'options'           => 'sometimes|array|nullable',
            'options.*'         => 'numeric',

            //TAB I (ОСНОВНОЕ)
            'mark_id'           => 'required|numeric',
            'brand_id'          => 'required|numeric',
            'complectation_id'  => 'required|numeric',
            'color_id'          => 'required|numeric',
            'year'              => 'required|numeric',
            'vin'               => 'sometimes|string|nullable',
            'order_number'      => 'sometimes|string|nullable',
            'marker_id'         => 'sometimes|numeric|nullable',
            'trade_marker_id'   => 'required|numeric',
            'provider_id'       => 'required|numeric',

            //TAB II (ЛОГИСТИКА)
            'order_type_id'     => 'sometimes|numeric|nullable',
            'order_date'        => 'sometimes|date_format:d.m.Y|nullable',
            'plan_date'         => 'sometimes|date_format:d.m.Y|nullable',
            'build_date'        => 'sometimes|date_format:d.m.Y|nullable',
            'ready_date'        => 'sometimes|date_format:d.m.Y|nullable',
            'request_date'      => 'sometimes|date_format:d.m.Y|nullable',
            'shipment_date'     => 'sometimes|date_format:d.m.Y|nullable',
            'stock_date'        => 'sometimes|date_format:d.m.Y|nullable',
            'technic_id'        => 'sometimes|numeric|nullable',
            'presale_date'      => 'sometimes|date_format:d.m.Y|nullable',
            'audio_code'        => 'sometimes|string|nullable',

            //TAB III (Складской учет)
            'invoice_date'      => 'sometimes|date_format:d.m.Y|nullable',
            'ransom_date'       => 'sometimes|date_format:d.m.Y|nullable',
            'off_date'          => 'sometimes|date_format:d.m.Y|nullable',
            'purchase_cost'     => 'sometimes|numeric|nullable',

            //delivery terms
            'delivery_term_id'    => 'sometimes|nullable',

            //Detailing cost
            'detailing_costs'                       => 'sometimes|array|nullable',
            'detailing_costs.*.detailing_cost_id'   => 'numeric|nullable|nullable',
            'detailing_costs.*.price'               => 'numeric|nullable|nullable',
            'detailing_costs.*.coefficient'         => 'numeric|nullable|nullable',

            //TUNING
            'tuning'            => 'sometimes|array|nullable',
            'tuning.price'      => 'nullable|numeric',
            'tuning.devices'    => 'array',
            'tuning.devices.*'  => 'numeric',

            'disable_sale'      => 'sometimes|boolean|nullable',
            'disable_off'       => 'sometimes|boolean|nullable',

            'collector_id'      => 'sometimes|nullable'
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
