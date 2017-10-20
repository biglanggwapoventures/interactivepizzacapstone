<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BeverageRequest extends FormRequest
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
            'description' => ['required', 'max:200'],
            'unit_price' => 'numeric',
        ];

        if ($this->isMethod('patch')) {
            $rules['description'][] = Rule::unique('ingredients')
                ->ignore($this->route('beverage'))
                ->where(function ($query) {
                    return $query->whereIsBeverage(1);
                });
        } else {
            $rules['description'][] = Rule::unique('ingredients')->where(function ($query) {
                return $query->whereIsBeverage(1);
            });
        }

        return $rules;
    }
}
