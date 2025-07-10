<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this as per your authorization logic
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'required|date',
            'phone_number' => 'nullable|string|max:15|unique:disabilities,phone_number',
            'region' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'woreda' => 'nullable|string|max:255',
            'hip_width' => 'required|numeric',
            'backrest_height' => 'required|numeric',
            'thigh_length' => 'required|numeric',
            'profile_image' => 'nullable|string',
            'id_image' => 'nullable|string',
            'is_provided' => 'boolean',
            'is_active' => 'boolean',
            'is_deleted' => 'boolean',

            // Nested Warrant Validation
            'warrant.first_name' => 'required|string|max:255',
            'warrant.middle_name' => 'nullable|string|max:255',
            'warrant.last_name' => 'required|string|max:255',
            'warrant.phone_number' => 'required|string|max:15',
            'warrant.gender' => 'nullable|in:male,female',
            'warrant.id_image' => 'nullable|string',

            // Nested Equipment Validation
            'equipment.type' => 'required|string|max:255',
            'equipment.size' => 'required|string|max:50',
            'equipment.cause_of_need' => 'required|string|max:255',
        ];
    }
}