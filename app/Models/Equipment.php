<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentSubType;
use App\Models\EquipmentType;


class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'size',
        'cause_of_need',
        'type_id',
        'sub_type_id',
    ];

    public function type()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function subType()
    {
        return $this->belongsTo(EquipmentSubType::class);
    }


}
