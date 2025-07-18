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
        return true; // Adjust if needed
    }

    public function rules()
    {
        $method = $this->method();
        $id = $this->route('id');

        // Helper to switch required/sometimes
        $req = $method === 'PATCH' ? 'sometimes|required' : 'required';

        return [
            'first_name' => "$req|string|max:255",
            'middle_name' => 'nullable|string|max:255',
            'last_name' => "$req|string|max:255",
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => "$req|date",
            'phone_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('disabilities', 'phone_number')->ignore($id),
            ],

            'region' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'woreda' => 'nullable|string|max:255',
            'hip_width' => "$req|numeric",
            'backrest_height' => "$req|numeric",
            'thigh_length' => "$req|numeric",
            'profile_image' => 'nullable|string',
            'id_image' => 'nullable|string',
            'is_provided' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',

            'warrant.first_name' => "$req|string|max:255",
            'warrant.middle_name' => 'nullable|string|max:255',
            'warrant.last_name' => "$req|string|max:255",
            'warrant.phone_number' => "$req|string|max:15",
            'warrant.gender' => 'nullable|in:male,female',
            'warrant.id_image' => 'nullable|string',

            'equipment.type_id' => "$req|string|max:255",
            'equipment.sub_type_id' => "$req|string|max:255",
            'equipment.size' => "$req|string|max:50",
            'equipment.cause_of_need' => "$req|string|max:255",
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
