<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DisabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this as needed
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'required|date',
            'phone_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('disabilities', 'phone_number')->ignore($this->route('id')),
            ],

            'region' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'woreda' => 'nullable|string|max:255',
            'hip_width' => 'required|numeric',
            'backrest_height' => 'required|numeric',
            'thigh_length' => 'required|numeric',
            'profile_image' => 'nullable|string',
            'id_image' => 'nullable|string',
            'is_provided' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',

            'warrant.first_name' => 'required|string|max:255',
            'warrant.middle_name' => 'nullable|string|max:255',
            'warrant.last_name' => 'required|string|max:255',
            'warrant.phone_number' => 'required|string|max:15',
            'warrant.gender' => 'nullable|in:male,female',
            'warrant.id_image' => 'nullable|string',

            'equipment.type' => 'required|string|max:255',
            'equipment.size' => 'required|string|max:50',
            'equipment.cause_of_need' => 'required|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
