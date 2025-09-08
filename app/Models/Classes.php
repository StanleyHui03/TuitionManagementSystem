<?php
// app/Models/Classes.php  (name class "Classes" to avoid PHP reserved 'class')
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Classes extends Model
{
    use HasPrefixedId;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'C';
    public const ID_PAD_LENGTH = 4;
    protected $fillable = ['subject_id'];

public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lessons::class, 'class_id', 'class_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollments::class, 'class_id', 'class_id');
    }

}
