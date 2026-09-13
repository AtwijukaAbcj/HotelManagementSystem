<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'guest_id', 'room_id', 'booking_id',
        'arrival_date', 'departure_date', 'checked_in_at', 'checked_out_at',
        'status', 'nightly_rate', 'notes',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'nightly_rate' => 'decimal:2',
    ];

    public function room()
    {
        return $this->belongsTo(addrooms::class, 'room_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
