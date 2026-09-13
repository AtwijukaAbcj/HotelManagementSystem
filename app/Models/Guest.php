<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'first_name', 'last_name', 'email', 'phone',
        'id_number', 'country', 'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
