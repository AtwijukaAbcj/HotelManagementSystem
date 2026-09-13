<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\Guest;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PosOrder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\addrooms;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([Booking::class, Expense::class, Guest::class, InventoryItem::class, Invoice::class, Payment::class, PosOrder::class, PurchaseOrder::class, Supplier::class, addrooms::class] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
