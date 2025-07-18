<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EquipmentTypeResource;
use App\Http\Resources\EquipmentSubTypeResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'size' => $this->size,
            'cause_of_need' => $this->cause_of_need,
            'type' => new EquipmentTypeResource($this->whenLoaded('equipmentType')),
            'sub_type' => new EquipmentSubTypeResource($this->whenLoaded('equipmentSubType')),
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
