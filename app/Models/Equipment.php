namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_type',
        'size',
        'cause_of_need',
    ];

    public function disabilityRecords()
    {
        return $this->hasMany(Disability::class);
    }
}
