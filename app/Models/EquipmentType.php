<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentSubType;
use App\Models\Equipment;

class EquipmentType extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = ['name'];

    /**
     * Get all sub types under this type.
     */
    public function subTypes()
    {
        return $this->hasMany(EquipmentSubType::class);
    }

    /**
     * Get all equipment that belongs to this type.
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
