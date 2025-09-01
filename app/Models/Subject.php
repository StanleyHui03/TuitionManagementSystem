<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Subject extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'subject_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'SU'; // subject -> SU0001
    public const ID_PAD_LENGTH = 4;
    protected $fillable = ['subject_Name','subject_Description','duration_Hours','subject_Fee'];

public function classes()
    {
        return $this->hasMany(Classes::class, 'subject_id', 'subject_id');
    }

    public function materials()
    {
        return $this->hasMany(Materials::class, 'subject_id', 'subject_id');
    }

}

