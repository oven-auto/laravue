<?php

namespace App\Http\Requests\ServiceProduct;

use Illuminate\Foundation\Http\FormRequest;

class ServiceProductCreate extends FormRequest
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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'nullable|integer',
            'duration' => 'nullable|integer',
            'group_id' => 'nullable|integer',
            'appeal_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле название обязательно для заполнения',
            'price.integer' => 'Поле цена может должно быть целым числом (стоимость в руб.)',
            'duration.integer' => 'Поле продолжительность должно быть целым числом (кол-во минут)',
            'group_id.integer' => 'Поле группа должно быть ссылкой на группу',
            'appeal_id.required' => 'Поле обращение обязательно для заполнения',
            'appeal_id.integer' => 'Поле обращение должно быть ссылкой на группу',
        ];
    }
}
