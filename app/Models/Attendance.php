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
        'lesson_id',
        'student_id',
        'marked_by_tutor_id',
        'status',
        'session_date',
        'marked_at',
        'note',
    ];

    protected $casts = [
        'session_date' => 'date',
        'marked_at'    => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'lesson_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function markedByTutor()
    {
        return $this->belongsTo(Tutor::class, 'marked_by_tutor_id', 'tutor_id');
    }
}
