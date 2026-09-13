<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'payment_status', 'status'];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }
}