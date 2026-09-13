<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'supplier',
        'supplier_id',
        'quantity',
        'unit_cost',
        'status',
        'po_number',
        'purchase_date',
        'expected_delivery_date',
        'invoice_reference',
        'warehouse',
        'payment_terms',
        'payment_status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function lines()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplierRecord()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(PurchaseOrderStatusHistory::class)->latest();
    }

    public function getDisplayTotalAttribute(): float
    {
        return (float) ($this->total ?: ($this->quantity * $this->unit_cost));
    }
}
