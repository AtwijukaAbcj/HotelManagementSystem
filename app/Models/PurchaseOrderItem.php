<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'inventory_item_id',
        'quantity',
        'received_quantity',
        'unit',
        'unit_cost',
        'tax_rate',
        'discount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}