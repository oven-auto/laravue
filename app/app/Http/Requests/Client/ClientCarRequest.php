<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientCarRequest extends FormRequest
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
            'brand_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'body_work_id' => 'nullable|integer',
            'year' => 'nullable|digits:4',
            'odometer' => 'nullable|string',
            'register_plate' => 'nullable|string|max:12',
            'vin' => 'nullable|string|size:17'
        ];
    }

    public function messages()
    {
        return [
            'brand_id.required' => 'Бренд автомобиля обязателен',
            'brand_id.integer' => 'Бренд указан не верно, либо не указан вовсе',
            'mark_id.required' => 'Модель автомобиля обязательна',
            'mark_id.integer' => 'Модель указана не верно, либо не указана вовсе',
            'body_work_id.integer' => 'Кузов указан не верно',
            'year.digits' => 'Год выпуска должен быть числом, состоящим из 4 символов',
            'odometer.integer' => 'Пробег должен быть числом',
            'vin.size' => 'VIN состоит из 17 символов'
        ];
    }
}
