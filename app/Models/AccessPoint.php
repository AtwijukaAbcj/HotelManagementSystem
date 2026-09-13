<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPoint extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area', 'device_type', 'status', 'last_communication', 'is_enabled', 'notes'];

    protected $casts = ['last_communication' => 'datetime', 'is_enabled' => 'boolean'];

    public function getIsOfflineAttribute(): bool
    {
        return $this->status !== 'online' || !$this->last_communication || $this->last_communication->lt(now()->subMinutes(10));
    }
}
