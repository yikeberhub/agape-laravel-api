namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'gender',
        'id_image',
        'is_deleted',
    ];

    public function disabilityRecords()
    {
        return $this->hasMany(Disability::class);
    }
}