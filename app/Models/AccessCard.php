<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number', 'card_type', 'guest_id', 'user_id', 'department', 'status',
        'issued_at', 'expires_at', 'notes', 'permitted_areas', 'replaced_card_id',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'permitted_areas' => 'array',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(AccessLog::class);
    }

    public function getHolderNameAttribute(): string
    {
        return $this->guest?->full_name ?? $this->user?->name ?? 'Unassigned';
    }
}
