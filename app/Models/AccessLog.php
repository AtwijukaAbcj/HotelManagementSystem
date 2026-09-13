<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_card_id', 'guest_id', 'user_id', 'room_id', 'access_point',
        'area', 'event_type', 'result', 'reason', 'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(AccessCard::class, 'access_card_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(addrooms::class, 'room_id');
    }
}
