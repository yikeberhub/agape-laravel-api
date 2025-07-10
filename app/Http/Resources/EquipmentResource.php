<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}