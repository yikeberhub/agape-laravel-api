<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table='equipments';
    protected $fillable = [
        'type',
        'size',
        'cause_of_need',
    ];

    public function disabilityRecords()
    {
        return $this->hasMany(Disability::class);
    }
}
