<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Attendance extends Model
{
    use HasPrefixedId;

    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'A';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'marked_by_tutor_id', 'lesson_id', 'student_id', 'status'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lessons::class, 'lesson_id', 'lesson_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
