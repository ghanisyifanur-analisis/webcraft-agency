<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'expense_title',
        'expense_amount',
        'category', // domain, hosting, plugin, freelance, other
        'vendor_name',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
