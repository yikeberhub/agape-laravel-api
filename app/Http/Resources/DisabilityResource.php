<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisabilityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'region' => $this->region,
            'zone' => $this->zone,
            'city' => $this->city,
            'woreda' => $this->woreda,
            'hip_width' => $this->hip_width,
            'backrest_height' => $this->backrest_height,
            'thigh_length' => $this->thigh_length,
            'profile_image' => $this->profile_image
                ? asset('storage/disabilities/profileImages/' . $this->profile_image)
                : null,

            'id_image' => $this->id_image
                ? asset('storage/disabilities/idImages/' . $this->id_image)  // ← fixed here
                : null,

            'is_provided' => $this->is_provided,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'recorder' => new UserResource($this->whenLoaded('user')),
            'warrant' => new WarrantResource($this->whenLoaded('warrant')),
            'equipment' => new EquipmentResource($this->whenLoaded('equipment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
