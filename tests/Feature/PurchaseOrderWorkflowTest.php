<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_line_purchase_order_does_not_receive_stock_until_receiving(): void
    {
        $user = User::factory()->create(['usertype' => '1']);
        $milk = InventoryItem::create([
            'name' => 'Milk', 'category' => 'Food', 'unit' => 'litre', 'current_stock' => 5,
            'reorder_level' => 2, 'unit_price' => 3000, 'supplier' => 'Fresh Foods', 'status' => 'active',
        ]);
        $sugar = InventoryItem::create([
            'name' => 'Sugar', 'category' => 'Food', 'unit' => 'kg', 'current_stock' => 8,
            'reorder_level' => 2, 'unit_price' => 5000, 'supplier' => 'Fresh Foods', 'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/purchases', [
            'supplier' => 'Fresh Foods',
            'purchase_date' => '2026-09-12',
            'expected_delivery_date' => '2026-09-15',
            'warehouse' => 'Main Store',
            'payment_terms' => 'Net 30',
            'action' => 'submit',
            'items' => [
                ['inventory_item_id' => $milk->id, 'quantity' => 10, 'unit' => 'litre', 'unit_cost' => 2800, 'tax_rate' => 0, 'discount' => 0],
                ['inventory_item_id' => $sugar->id, 'quantity' => 4, 'unit' => 'kg', 'unit_cost' => 4500, 'tax_rate' => 0, 'discount' => 1000],
            ],
        ]);

        $response->assertRedirect('/purchases');
        $purchase = PurchaseOrder::with('lines')->firstOrFail();
        $this->assertCount(2, $purchase->lines);
        $this->assertSame('pending_approval', $purchase->status);
        $this->assertDatabaseHas('inventory_items', ['id' => $milk->id, 'current_stock' => 5]);
        $this->assertDatabaseHas('inventory_items', ['id' => $sugar->id, 'current_stock' => 8]);

        $milkLine = $purchase->lines->firstWhere('inventory_item_id', $milk->id);
        $sugarLine = $purchase->lines->firstWhere('inventory_item_id', $sugar->id);

        $this->actingAs($user)
            ->post("/purchases/{$purchase->id}/receive", [
                'received' => [$milkLine->id => 3, $sugarLine->id => 0],
            ])
            ->assertRedirect('/purchases');

        $this->assertDatabaseHas('inventory_items', ['id' => $milk->id, 'current_stock' => 8]);
        $this->assertDatabaseHas('purchase_orders', ['id' => $purchase->id, 'status' => 'partially_received']);

        $this->actingAs($user)
            ->post("/purchases/{$purchase->id}/receive", [
                'received' => [$milkLine->id => 8, $sugarLine->id => 4],
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->post("/purchases/{$purchase->id}/receive", [
                'received' => [$milkLine->id => 7, $sugarLine->id => 4],
            ])
            ->assertRedirect('/purchases');

        $this->assertDatabaseHas('inventory_items', ['id' => $milk->id, 'current_stock' => 15]);
        $this->assertDatabaseHas('inventory_items', ['id' => $sugar->id, 'current_stock' => 12]);
        $this->assertDatabaseHas('purchase_orders', ['id' => $purchase->id, 'status' => 'received']);
    }

    public function test_purchase_order_approval_and_rejection_are_recorded(): void
    {
        $admin = User::factory()->create(['usertype' => '1', 'role' => 'admin']);
        $item = InventoryItem::create([
            'name' => 'Cleaning liquid', 'category' => 'Housekeeping', 'unit' => 'bottle', 'current_stock' => 2,
            'reorder_level' => 1, 'unit_price' => 7000, 'supplier' => 'Clean Supply', 'status' => 'active',
        ]);

        $this->actingAs($admin)->post('/purchases', [
            'supplier' => 'Clean Supply', 'purchase_date' => '2026-09-13', 'warehouse' => 'Main Store',
            'action' => 'submit',
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 5, 'unit' => 'bottle', 'unit_cost' => 6500]],
        ]);

        $purchase = PurchaseOrder::firstOrFail();
        $this->actingAs($admin)->post("/purchases/{$purchase->id}/approve")->assertRedirect('/purchases');
        $this->assertDatabaseHas('purchase_order_status_histories', ['purchase_order_id' => $purchase->id, 'action' => 'approved']);
    }
}
