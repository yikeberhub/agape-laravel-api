<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Warrant;
use App\Models\Equipment;

class Disability extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'region',
        'zone',
        'city',
        'woreda',
        'recorder_id',
        'warrant_id',
        'equipment_id',
        'hip_width',
        'backrest_height',
        'thigh_length',
        'profile_image',
        'id_image',
        'is_provided',
        'is_active',
        'is_deleted',
    ];

    public function user(){
    return $this->belongsTo(User::class, 'recorder_id');
}

    public function warrant()
    {
        return $this->belongsTo(Warrant::class, 'warrant_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    
}