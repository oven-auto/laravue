<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      required={"journal"}
 * )
 */
class DiscountListRequest extends FormRequest
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
     * @OA\Property(
     *  property="journal", 
     *  type="string", 
     *  format="string", 
     *  description="Тип журнала, значения для подстановки: newcar, oldcar, service, parts",
     *  enum={"newcar","oldcar", "service", "parts"}
     * ),
     */
    public function rules()
    {
        return [
            "journal" => "required|in:newcar, oldcar, service, parts"
        ];
    }
}
