<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_and_inventory_routes_require_authentication(): void
    {
        $this->get('/pos')->assertRedirect('/login');
        $this->get('/inventory')->assertRedirect('/login');
        $this->get('/purchases')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_inventory_and_purchase_records(): void
    {
        $user = User::factory()->create(['usertype' => '1']);

        $inventoryResponse = $this->actingAs($user)->post('/inventory', [
            'name' => 'Coffee Beans',
            'category' => 'Beverage',
            'unit' => 'kg',
            'current_stock' => 15,
            'reorder_level' => 5,
            'unit_price' => 18.50,
            'supplier' => 'Prime Supply',
            'status' => 'active',
        ]);

        $inventoryResponse->assertRedirect('/inventory');
        $this->assertDatabaseHas('inventory_items', ['name' => 'Coffee Beans']);

        $purchaseResponse = $this->actingAs($user)->post('/purchases', [
            'item_id' => InventoryItem::first()->id,
            'supplier' => 'Prime Supply',
            'quantity' => 10,
            'unit_cost' => 16.00,
            'status' => 'received',
            'notes' => 'Monthly restock',
        ]);

        $purchaseResponse->assertRedirect('/purchases');
        $this->assertDatabaseHas('purchase_orders', ['supplier' => 'Prime Supply']);
    }

    public function test_pos_sale_creates_order_and_reduces_stock(): void
    {
        $user = User::factory()->create(['usertype' => '1']);
        $item = InventoryItem::create([
            'name' => 'House Coffee',
            'category' => 'Beverage',
            'unit' => 'cup',
            'current_stock' => 10,
            'reorder_level' => 2,
            'unit_price' => 4.50,
            'supplier' => 'Prime Supply',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post('/pos', ['items' => [['id' => $item->id, 'quantity' => 2]]])
            ->assertRedirect('/pos');

        $this->assertDatabaseHas('pos_orders', [
            'user_id' => $user->id,
            'total' => 9.00,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('pos_order_items', [
            'inventory_item_id' => $item->id,
            'quantity' => 2,
            'line_total' => 9.00,
        ]);
        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'current_stock' => 8]);
    }
}
