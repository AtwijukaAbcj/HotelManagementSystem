<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_name',
        'invoice_number',
        'amount',
        'status',
        'payment_method',
        'notes',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
