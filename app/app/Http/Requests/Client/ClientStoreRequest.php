<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
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
            'firstname' => 'nullable|alpha',
            'lastname' => 'nullable|alpha',
            'fathername' => 'nullable|alpha',
            'client_type_id' => 'required|numeric|integer',
            'trafic_sex_id' => 'nullable|numeric|integer',
            'trafic_zone_id' => 'nullable|numeric|integer',
            'contacts' => 'array|required',
            'contacts.0.phone' => 'required|string',
            'contacts.*.phone' => 'string|nullable|unique:App\Models\ClientPhone,phone',
            'contacts.*.email' => 'string|nullable|unique:App\Models\ClientEmail,email',
        ];
    }

    public function message()
    {
        return [
            'firstname.alpha' => 'Имя может состоять только из букв',
            'lastname.alpha' => 'Фамилия может состоять только из букв',
            'fathername.alpha' => 'Отчество может состоять только из букв',
            'trafic_sex_id.required' => 'Не указан тип клиента (Физ./Юр. лицо)',
            'contacts.required' => 'Не указан контакт клиента',
            'contacts.0.phone.required' => 'Должен быть указан номер телефона',
            'contacts.*.phone' => ['unique' => 'Поле телефон не уникально, такой телефон уже имеется в базе клиентов'],
            'contacts.*.email' => ['unique' => 'Поле Email не уникально, такой адрес уже имеется в базе клиентов'],
        ];
    }
}
