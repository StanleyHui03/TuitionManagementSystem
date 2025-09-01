<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId; // if you really have this trait

class Payment extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // If your table is named `payments`, you can omit the next line.
    // protected $table = 'payments';

    public const ID_PREFIX = 'P';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = ['student_id', 'paymentTotal', 'paymentDate', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'payment_id', 'payment_id');
    }
}
