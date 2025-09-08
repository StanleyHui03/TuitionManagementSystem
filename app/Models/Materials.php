<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Materials extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'material_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'M';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'tutor_id', 'subject_id', 'title', 'file_path', 'original_file_name'
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'tutor_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }
}
