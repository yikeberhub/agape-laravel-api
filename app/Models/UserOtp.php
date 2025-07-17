<?php 

// app/Models/UserOtp.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $fillable = ['user_id', 'otp', 'expires_at'];
    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class);
    }
}
