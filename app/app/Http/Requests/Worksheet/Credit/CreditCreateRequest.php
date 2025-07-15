<?php

namespace App\Http\Requests\Worksheet\Credit;

use App\Http\DTO\Worksheet\Credit\CreateCreditDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      
 * )
 */ 
class CreditCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
    * @OA\Property(
    *      format="integer",    
    *      description="ID рабочего листа",     
    *      property="worksheet",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ID заемщика",     
    *      property="debtor",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ID тактики расчета",     
    *      property="tactic",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ID кредитора",     
    *      property="creditor",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ID статуса заявки",     
    *      property="status",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ID автора заявки",     
    *      property="author",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="boolean",    
    *      description="расторжение",     
    *      property="close",        
    *      type="boolean"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="Срок",     
    *      property="period",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="Стоимость",     
    *      property="cost",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="ПВ",     
    *      property="first_pay",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="Месячный платеж",     
    *      property="month_pay",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="boolean",    
    *      description="Упрощнный растет",     
    *      property="simple",        
    *      type="boolean"
    * ),

     * @OA\Property(
     *  description="Наполнение (примерное) те сервисы (именно сервисы, а не модули РЛ), которые возможно добавятся (api/finservices)",     
     *  property="approximates",   
     *  type="array", 
     *  @OA\Items(type="integer", example={1,2})
     * ),

    * @OA\Property(
    *      format="string",    
    *      description="Дата оформления",     
    *      property="register_at",        
    *      type="string"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="Оформитель",     
    *      property="decorator",        
    *      type="integer"
    * ),
 
    * @OA\Property(
    *      format="integer",    
    *      description="Награда",     
    *      property="award",        
    *      type="integer"
    * ),

    * @OA\Property(
    *      format="boolean",    
    *      description="Начисление награды",     
    *      property="award_complete",        
    *      type="boolean"
    * ),

    * @OA\Property(
    *      format="integer",    
    *      description="удержание",     
    *      property="deduction",        
    *      type="integer"
    * ),

     * @OA\Property(
     *  description="Модули финансовых услуг (те финуслуги что есть в рл)",     
     *  property="services",   
     *  type="array", 
     *  @OA\Items(type="integer", example={1,2})
     * ),

     * @OA\Property(
     *  description="Машина",     
     *  property="car",   
     *  type="array", 
     *  @OA\Items(type="integer", example={"id" = "2", "type" = "new"})
     * ),
     */

    public function rules(): array
    {
        return [
            
            'worksheet'         => 'required|numeric',
            
            'debtor'            => 'required|numeric',
            
            'tactic'            => 'required|numeric',
            
            'creditor'          => 'required|numeric',
          
            'status'            => 'required|numeric',
        
            'author'            => 'required|numeric',
        
            'close'             => 'sometimes|numeric',
          
            'period'            => 'sometimes|numeric',
           
            'cost'              => 'required_if:period|numeric',
            
            'first_pay'         => 'required_if:period|numeric',
            
            'month_pay'         => 'required_if:period|numeric',
        
            'simple'            => 'required_if:period|numeric',
      
            'approximates'      => 'sometimes|array',
      
            'register_at'       => 'sometimes|date_format:d.m.Y',
        
            'decorator'         => 'required_if:register_at|numeric',
           
            'award'             => 'sometimes|numeric',            
            
            'award_complete'    => 'required_if:award|numeric',
           
            'deduction'         => 'sometimes|numeric',
            
            'services'          => 'sometimes|array',
            
            'car'               => 'array',
            'car.type'          => 'string|required|in:new,client,used',
            'car.id'            => 'required|numeric',
        ];
    }



    public function getDTO()
    {
        return CreateCreditDTO::fromArray($this->validated());
    }
}
