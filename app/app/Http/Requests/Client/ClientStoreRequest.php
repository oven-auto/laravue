<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Route;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

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
        $message = '';

        $client = Route::current()->parameter('client');
        $clientId = $client ? $client->id : 0;

        if(request()->has('contacts'))
            foreach(request()->contacts as $item){
                if(isset($item['phone']))
                    $data['phones'][] = preg_replace("/[^,.0-9]/", '', $item['phone']);
                if(isset($item['email']))
                    $data['emails'][] = $item['email'];
            }

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
            'firstname' => 'nullable|alpha',
            'lastname' => 'nullable|alpha',
            'fathername' => 'nullable|alpha',
            'client_type_id' => 'required|numeric|integer',
            'trafic_sex_id' => 'nullable|numeric|integer',
            'trafic_zone_id' => 'nullable|numeric|integer',
            'birthday_at' => 'nullable|date',
            'driver_license_issue_at' => 'nullable|date',
            'passport_issue_at' => 'nullable|date',
            'address' => 'nullable|string',
            'driving_license' => 'nullable|regex:([0-9]{4}\s{1}[0-9]{6})',
            'serial_number' => 'nullable|regex:([0-9]{4}\s{1}[0-9]{6})',
            'contacts' => 'array|required',

            'contacts.*.phone' => [
                'distinct',
                'string',
                'nullable',
                'regex:([+]{1}[7]{1}\s{1}[(]{1}[0-9]{3}[)]{1}\s{1}[0-9]{3}[-]{1}[0-9]{2}[-]{1}[0-9]{2})',

            ],
        ];


            if(request()->get('client_type_id') == 1) {
                $arr2 = [
                    'contacts' => 'array|required',
                    'contacts.0.phone' => 'required',
                    'contacts.*.phone' => [
                        'distinct',
                        'string',
                        'nullable',
                        'regex:([+]{1}[7]{1}\s{1}[(]{1}[0-9]{3}[)]{1}\s{1}[0-9]{3}[-]{1}[0-9]{2}[-]{1}[0-9]{2})',

                    ],
                    'contacts.*.email' => [
                        'distinct',
                        'string',
                        'nullable',
                        'email:rfc,dns',
                    ],
                ];
                return array_merge($arr2, $arr);
            }

            elseif(request()->get('client_type_id') == 2) {
                $arr2 = [
                    'url' => 'nullable',
                    'inn' => 'required',
                    'company_name' => 'required',
                    'contacts' => 'array|nullable',
                    'contacts.*.phone' => [
                        'distinct',
                        'string',
                        'nullable',
                        'regex:([+]{1}[7]{1}\s{1}[(]{1}[0-9]{3}[)]{1}\s{1}[0-9]{3}[-]{1}[0-9]{2}[-]{1}[0-9]{2})',

                    ],
                    'contacts.*.email' => [
                        'distinct',
                        'string',
                        'nullable',
                        'email:rfc,dns',
                    ],
                ];
                return array_merge($arr2, $arr);
            }
    }

    public function messages()
    {
        $client = Route::current()->parameter('client');
        return [
            'firstname.alpha' => 'Имя может состоять только из букв',
            'lastname.alpha' => 'Фамилия может состоять только из букв',
            'fathername.alpha' => 'Отчество может состоять только из букв',
            'trafic_sex_id.required' => 'Не указан тип клиента (Физ./Юр. лицо)',

            'contacts.required' => 'Не указан контакт клиента',
            'contacts.0.phone.required' => 'Должен быть указан номер телефона',
            'contacts.*.phone.string' => 'Телефон должен иметь формат +7 (XXX) XXX-XX-XX',
            'contacts.*.phone.regex' => 'Телефон должен иметь формат +7 (XXX) XXX-XX-XX ',
            'contacts.*.phone.unique' => 'Поле телефон не уникально, такой телефон уже имеется в базе клиентов',
            'contacts.0.phone.distinct' => 'Вы указали повторяющиеся телефоны',
            'contacts.*.email.unique' => 'Поле Email не уникально, такой адрес уже имеется в базе клиентов',
            'contacts.*.email.distinct' => 'Вы указали повторяющиеся Email',
            'contacts.*.email.email' => 'Поле Email не может быть в переданном формате',

            'birthday_at.date' => 'Формат даты дня рождения DD.MM.YYYY',
            'driver_license_issue_at.date' => 'Формат даты выдачи вод. уд DD.MM.YYYY',
            'passport_issue_at.date' => 'Формат даты выдачи паспорта DD.MM.YYYY',

            'driving_license.regex' => 'Формат серии и номера вод. уд. XXXX XXXXXX',
            'serial_number.regex' => 'Формат серии и номера паспорта XXXX XXXXXX',
        ];
    }
}
