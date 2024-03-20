<?php

namespace App\Http\Requests\Trafic;

use Illuminate\Foundation\Http\FormRequest;

class TraficCreateRequest extends FormRequest
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
            'company_id'            => 'required',
            'company_structure_id'  => 'required',
            'trafic_appeal_id'      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'company_id.required' => 'Поле салон обязательно для заполнения',
            'company_structure_id.required' => 'Поле подразделение обязательно для заполнения',
            'trafic_appeal_id.required' => 'Поле цель обращения обязательно для заполнения',
        ];
    }
}
