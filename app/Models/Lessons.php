<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Lessons extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'lesson_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'L';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'tutor_id', 'class_id', 'start_at', 'ends_at', 'room'
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'tutor_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'lesson_id', 'lesson_id');
    }
}
