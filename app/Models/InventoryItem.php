<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'current_stock',
        'reorder_level',
        'unit_price',
        'supplier',
        'supplier_id',
        'status',
    ];

    public function purchases()
    {
        return $this->hasMany(PurchaseOrder::class, 'item_id');
    }

    public function supplierRecord()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
