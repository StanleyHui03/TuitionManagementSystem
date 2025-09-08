<?php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Student extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'student_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'S';
    public const ID_PAD_LENGTH = 4;
    protected $fillable = ['user_id','studentName','phoneNum','address','gender'];

public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollments::class, 'student_id', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'student_id', 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

}
