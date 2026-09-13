<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'title',
        'description',
        'priority',
        'status',
        'requested_by',
        'assigned_to',
    ];

    public function room()
    {
        return $this->belongsTo(addrooms::class, 'room_id');
    }
}
