<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'room_type',
        'room_number',
        'date',
        'time',
        'arrival_date',
        'departure_date',
        'email_id',
        'ph_number',
        'message',
        'status',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function stay()
    {
        return $this->hasOne(Stay::class);
    }
}
