<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id', 'receipt_number', 'name', 'email', 'room_type', 'room_number',
        'billing_date', 'billing_time', 'no_of_days_stay', 'price', 'total',
        'transaction_type', 'receipt_email_status', 'receipt_sent_at',
    ];

    protected $casts = ['receipt_sent_at' => 'datetime'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
