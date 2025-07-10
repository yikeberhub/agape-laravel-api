<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'phone_number' => 'nullable|string',
            'region' => 'required|string',
            'zone' => 'required|string',
            'city' => 'required|string',
            'woreda' => 'required|string',
            'hip_width' => 'required|numeric',
            'backrest_height' => 'required|numeric',
            'thigh_length' => 'required|numeric',
            'profile_image' => 'nullable|string',
            'id_image' => 'nullable|string',
            'is_provided' => 'boolean',
            'is_active' => 'boolean|default:true',
            'warrant_id' => 'required|exists:warrants,id',
            'equipment_id' => 'nullable|exists:equipment,id',
        ];
    }
}