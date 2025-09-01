<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Enrollments extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'enrollment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'E';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'class_id', 'student_id'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
