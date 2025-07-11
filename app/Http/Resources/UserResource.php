<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'profile_image'=>$this->profile_image?asset('storage/users/profileImages/'.$this->profile_image):null,
            "phone_number"=>$this->phone_number,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'gender'=>$this->gender,
            'country'=>$this->country,
            'region'=>$this->region,
            'city'=>$this->city,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}