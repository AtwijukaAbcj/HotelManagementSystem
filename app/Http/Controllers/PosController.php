<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\PosOrder;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $items = InventoryItem::where('status', 'active')->latest()->get();
        $orders = PosOrder::with('items.item')->latest()->take(10)->get();

        return view('admin.pos.index', compact('items', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        $validated['items'] = array_values(array_filter(
            $validated['items'],
            fn (array $line): bool => (float) $line['quantity'] > 0
        ));

        if ($validated['items'] === []) {
            return back()->withErrors(['items' => 'Select at least one item quantity.']);
        }

        DB::transaction(function () use ($validated, $request) {
            $total = 0;
            $lines = [];

            foreach ($validated['items'] as $line) {
                $item = InventoryItem::whereKey($line['id'])->lockForUpdate()->firstOrFail();

                if ($item->status !== 'active' || $item->current_stock < $line['quantity']) {
                    abort(422, "Insufficient stock for {$item->name}.");
                }

                $lineTotal = $item->unit_price * $line['quantity'];
                $total += $lineTotal;
                $lines[] = [$item, $line['quantity'], $lineTotal];
            }

            $order = PosOrder::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            foreach ($lines as [$item, $quantity, $lineTotal]) {
                $order->items()->create([
                    'inventory_item_id' => $item->id,
                    'quantity' => $quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $lineTotal,
                ]);
                $stockBefore = (float) $item->current_stock;
                $item->decrement('current_stock', $quantity);
                StockMovement::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => $request->user()->id,
                    'movement_type' => 'pos_sale',
                    'quantity' => -$quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - (float) $quantity,
                    'reference_type' => PosOrder::class,
                    'reference_id' => $order->id,
                    'notes' => 'POS sale #' . $order->id,
                ]);
            }
        });

        return redirect()->route('pos.index')->with('message', 'Sale completed and inventory updated.');
    }
}
