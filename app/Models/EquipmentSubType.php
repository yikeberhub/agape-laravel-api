<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentType;
use App\Models\Equipment;

class EquipmentSubType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'equipment_type_id'];

    /**
     * Get the parent type of this sub-type.
     */
    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    /**
     * Get all equipment that belongs to this sub-type.
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
