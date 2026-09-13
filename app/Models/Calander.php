<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calander extends Model
{
    use HasFactory;

    protected $fillable = ['event_name', 'event_date'];

    protected $casts = ['event_date' => 'date'];
}
