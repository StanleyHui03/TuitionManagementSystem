<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasPrefixedId;

class Payments extends Model
{
    use HasPrefixedId, SoftDeletes;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'P';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'student_id',
        'paymentTotal',
        'paymentDate',
        'status',
        'description',
    ];

    protected $casts = [
        'paymentDate' => 'date',
        'paymentTotal' => 'decimal:2',
        'description' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $lastId = self::withTrashed()->max('payment_id'); // includes soft-deleted
                $next = $lastId ? (int) substr($lastId, strlen(self::ID_PREFIX)) + 1 : 1;
                $payment->payment_id = self::ID_PREFIX . str_pad($next, self::ID_PAD_LENGTH, '0', STR_PAD_LEFT);
            }
        });
    }

    /* ---------------- Relationships ---------------- */

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'tutor_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'payment_id', 'payment_id');
    }

    /* ---------------- Scopes (optional helpers) ---------------- */

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeOnlyDeleted($query)
    {
        return $query->onlyTrashed();
    }

    
}
