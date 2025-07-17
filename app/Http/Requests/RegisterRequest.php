<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function prepareForValidation()
    {
        $this->merge([
            'password' => $this->input('password', 'password')
        ]);
    }

    public function rules()
{
    return [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'first_name' => 'required|string|max:255',
        'last-name'=> 'required|string|max:255',
        'role' => 'required|string|max:50|in:admin,field_worker',
        'gender' => 'required|string|in:male,female',
        'phone_number' => 'required|string|max:11',
    ];
}


    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'errors' => $this->formatErrors($validator->errors()),
        ],422);

        throw new ValidationException($validator, $response);
    }

    protected function formatErrors($errors)
    {
        return $errors->toArray(); 
    }
}