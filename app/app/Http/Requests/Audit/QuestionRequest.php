<?php

namespace App\Http\Requests\Audit;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'audit_id'              =>       'required',
            'name'                  =>       'required',
            'text'                  =>       'required',
            'audit_id'              =>       'required',
            'weight'                =>       'sometimes|numeric',
            'is_stoped'             =>       'sometimes|numeric',
            'answers'               =>       'array|required',
            'answers.positive'      =>       'required',
            'answers.negative'      =>       'sometimes',
            'answers.neutral'       =>       'sometimes',
        ];
    }
}
