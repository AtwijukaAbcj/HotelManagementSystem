<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'guest_id',
        'payer_name',
        'payer_email',
        'reference',
        'amount',
        'method',
        'status',
        'paid_at',
        'notes',
        'receipt_email_status',
        'receipt_sent_at',
    ];

    protected $casts = ['paid_at' => 'datetime', 'amount' => 'decimal:2', 'receipt_sent_at' => 'datetime'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}