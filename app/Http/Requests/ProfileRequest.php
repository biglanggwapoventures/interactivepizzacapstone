<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'firstname' => '|sometimes|required',
            'lastname' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'contact_number' => 'required',
            'street_number' => 'required',
            'barangay' => 'required',
            'city' => 'required',
            'old_password' => 'sometimes|required',
            'password' => 'sometimes|required|min:6',
            'password_confirmation' => 'sometimes|required|same:password'
        ];
    }
}
