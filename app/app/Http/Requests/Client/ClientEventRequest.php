<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientEventRequest extends FormRequest
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
        $method = 'POST';
        $methods = \Route::current()->methods();
        if(in_array('POST',$methods))
            $method = 'POST';
        if(in_array('PATCH', $methods))
            $method = 'PATCH';

            if($method == 'POST')
                $arr = [
                    'client_id' => 'required',
                    'date_at' => 'required',
                    'title' => 'required',
                    'group_id' => 'required',
                    'type_id' => 'required',
                    'text' => 'required',
                    'executors' => 'nullable',
                    'begin_time' => 'nullable|date_format:H:i',
                    'end_time' => 'nullable|date_format:H:i',
                ];

            elseif($method == 'PATCH')
                $arr = [
                    'date_at' => 'required',
                    'title' => 'required',
                    'group_id' => 'required',
                    'type_id' => 'required',
                    'text' => 'nullable',
                    'executors' => 'nullable|array',
                    'begin_time' => 'nullable|date_format:H:i',
                    'end_time' => 'nullable|date_format:H:i',
                ];

            return $arr;
    }

    public function messages()
    {
        return [
            'date_at.required' => 'Дата назначения обязательна',
            'title.required' => 'Заголовок обязателен',
            'group_id.required' => 'Группа обязательна',
            'type_id.required'  => 'Тип обязателен',
            'text.required'  => 'Комментарий обязателен для заполнения',
            'executors.array' => 'Ответственные внесены не верно',
        ];
    }
}
