<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addrooms extends Model
{
    use HasFactory;

    protected $table = 'addrooms';

    protected $fillable = [
        'room_number',
        'floor',
        'price',
        'room_type',
        'property_id',
        'operational_status',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function stays()
    {
        return $this->hasMany(Stay::class, 'room_id');
    }
}
