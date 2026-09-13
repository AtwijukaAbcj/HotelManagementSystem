<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}