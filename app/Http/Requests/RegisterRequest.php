<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

   
    public function rules()
    {
        return [
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8|confirmed',
            'first_name'=>'required|string|max:255',
            'role'=>'required|string|max:50',
            'phone_number'=>'required|string|max:11',
        ];
    }
}
