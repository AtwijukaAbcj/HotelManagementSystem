<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousekeepingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'task_type',
        'assignee',
        'priority',
        'status',
        'notes',
    ];

    public function room()
    {
        return $this->belongsTo(addrooms::class, 'room_id');
    }
}
