<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'property_id',
        'guest_id',
        'room_id',
        'stay_id',
        'table_reference',
        'subtotal',
        'discount',
        'tax',
        'service_charge',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'amount_received',
        'change_due',
        'notes',
        'receipt_email',
        'receipt_email_status',
        'receipt_sent_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'service_charge' => 'float',
        'total' => 'float',
        'amount_received' => 'float',
        'change_due' => 'float',
        'receipt_sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(addrooms::class, 'room_id');
    }

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}