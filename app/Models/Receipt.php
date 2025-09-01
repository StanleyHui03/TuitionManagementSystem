<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;
use Payment;

class Receipt extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'receipt_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'R';
    public const ID_PAD_LENGTH = 4;

    protected $fillable = [
        'payment_id', 'subTotal', 'receiptDate'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
}

