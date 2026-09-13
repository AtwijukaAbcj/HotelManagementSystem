<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'contact_name', 'email', 'phone', 'address', 'status', 'notes'];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}