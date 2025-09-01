<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Tutor extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'tutor_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'T';
    public const ID_PAD_LENGTH = 4;
    protected $fillable = ['user_id','tutor_name','tutor_email','phoneNumber'];

public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lessons::class, 'tutor_id', 'tutor_id');
    }

    public function materials()
    {
        return $this->hasMany(Materials::class, 'tutor_id', 'tutor_id');
    }

}

