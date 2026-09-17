<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_name',
        'guest_id',
        'invoice_number',
        'amount',
        'status',
        'payment_method',
        'notes',
        'receipt_email',
        'receipt_email_status',
        'receipt_sent_at',
    ];

    protected $casts = ['receipt_sent_at' => 'datetime'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
