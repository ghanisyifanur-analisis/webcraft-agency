<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_channel', // VA_BCA, VA_MANDIRI, VA_BRI, QRIS, MANUAL_TRANSFER
        'reference_code',
        'amount_paid',
        'paid_at',
        'status', // success, pending, failed
        'gateway_payload',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_payload' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
