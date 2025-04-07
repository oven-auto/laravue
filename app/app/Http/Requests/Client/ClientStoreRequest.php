<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
        $data = ['phones' => [], 'emails' => []];

        $phones = request()->get('phones');
        foreach($phones as $item)
        {
            if(in_array($item['phone'], $data['phones']))
                throw new \Exception('Одинаковые телефоны.');
            $data['phones'][] = $item['phone'];
        }

        if(request()->has('emails'))
            $data['emails'] = request()->get('emails');

        $message = '';

        $client = Route::current()->parameter('client');
        $clientId = $client ? $client->id : 0;

        if(request()->has('inn')) {
            $uniqueInn = \App\Models\ClientInn::with('client')
                ->where('number',request()->inn)
                ->where('client_id', '<>', $clientId)
                ->get();
            foreach($uniqueInn as $item)
                $message.='ИНН '.$item->number.' не уникален ('.$item->client_id.' : '.$item->client->company_name.')'.PHP_EOL;
        }

        $uniquePhone = \App\Models\ClientPhone::with('client')
            ->whereIn('phone',$data['phones'])
            ->where('client_id', '<>', $clientId)
            ->get();

        $uniqueEmail = \App\Models\ClientEmail::with('client')
            ->whereIn('email',$data['emails'])
            ->where('client_id', '<>', $clientId)
            ->get();

        foreach($uniquePhone as $item)
            $message.='Телефон '.$item->phone_mask.' не уникален ('.$item->client_id.' : '.$item->client->full_name.')'.PHP_EOL;
        foreach($uniqueEmail as $item)
            $message.='Email '.$item->email.' не уникален ('.$item->client_id.' : '.$item->client->full_name.')'.PHP_EOL;



        if($message)
            throw new \Exception($message);

        $arr = [
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
            'fathername' => 'nullable|string',
            'client_type_id' => 'required|numeric|integer',
            'trafic_sex_id' => 'nullable|numeric|integer',
            'trafic_zone_id' => 'nullable|numeric|integer',
            'birthday_at' => 'nullable|date',
            'driver_license_issue_at' => 'nullable|date',
            'passport_issue_at' => 'nullable|date',
            'address' => 'nullable|string',
            'driving_license' => 'nullable|regex:([0-9]{4}\s{1}[0-9]{6})',
            'serial_number' => 'nullable|regex:([0-9]{4}\s{1}[0-9]{6})',
            'form_owner_id' => 'sometimes|numeric|nullable',
        ];


            if(request()->get('client_type_id') == 1) {
                $arr = array_merge([
                    'phones' => 'array|required',
                    'emails' => 'nullable|array'
                ], $arr);
            }

            if(request()->get('client_type_id') == 2) {
                $arr = array_merge([
                    'url' => 'nullable',
                    'inn' => 'required',
                    'company_name' => 'required',
                ], $arr);
            }

        return $arr;
    }



    public function messages()
    {
        $client = Route::current()->parameter('client');
        return [
            'firstname.string' => 'Имя может состоять только из букв',
            'lastname.string' => 'Фамилия может состоять только из букв',
            'fathername.alpha' => 'Отчество может состоять только из букв',
            'trafic_sex_id.required' => 'Не указан тип клиента (Физ./Юр. лицо)',
            'birthday_at.date' => 'Формат даты дня рождения DD.MM.YYYY',
            'driver_license_issue_at.date' => 'Формат даты выдачи вод. уд DD.MM.YYYY',
            'passport_issue_at.date' => 'Формат даты выдачи паспорта DD.MM.YYYY',
            'driving_license.regex' => 'Формат серии и номера вод. уд. XXXX XXXXXX',
            'serial_number.regex' => 'Формат серии и номера паспорта XXXX XXXXXX',
        ];
    }
}



