<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PizzaIngredientsRequest extends FormRequest
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
        $rules = [
            'unit_price' => 'required|numeric',
            'size' => 'required|in:SMALL,MEDIUM,LARGE',
            'item' => 'array|required',
            'item.*.id' => 'sometimes|exists:ingredients,id|required_with:item.*.quantity',
            'item.*.quantity' => 'sometimes|numeric|required_with:item.*.id'
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'item.required' => 'There must be atleast 1 ingredient',
        ];
    }
}
