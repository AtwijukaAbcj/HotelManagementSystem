<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\PurchaseOrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::latest()->get();
        $lowStockItems = $items->filter(fn (InventoryItem $item) => (float) $item->current_stock <= (float) $item->reorder_level);

        return view('admin.inventory.index', compact('items', 'lowStockItems'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function edit(InventoryItem $item)
    {
        return view('admin.inventory.edit', compact('item'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:80',
            'unit' => 'required|string|max:20',
            'reorder_level' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:150',
            'status' => 'required|in:active,inactive',
        ]);

        $item->update($validated);

        return redirect()->route('inventory.index')->with('message', 'Inventory item updated successfully.');
    }

    public function purchases()
    {
        $query = PurchaseOrder::with('lines.item')->latest();

        if ($search = request('search')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('po_number', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('invoice_reference', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($supplier = request('supplier')) {
            $query->where('supplier', $supplier);
        }

        if (request('date_from')) {
            $query->whereDate('purchase_date', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('purchase_date', '<=', request('date_to'));
        }

        $purchases = $query->paginate(10)->withQueryString();
        $allPurchases = PurchaseOrder::get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->pluck('name');
        $summary = [
            'total' => $allPurchases->count(),
            'pending' => $allPurchases->whereIn('status', ['pending_approval', 'approved'])->count(),
            'awaiting' => $allPurchases->whereIn('status', ['ordered', 'partially_received'])->count(),
            'received' => $allPurchases->where('status', 'received')->count(),
            'spend' => $allPurchases->sum(fn (PurchaseOrder $purchase) => $purchase->display_total),
        ];

        return view('admin.inventory.purchases', compact('purchases', 'suppliers', 'summary'));
    }

    public function movements()
    {
        $movements = StockMovement::with(['item', 'user'])->latest()->paginate(20);

        return view('admin.inventory.movements', compact('movements'));
    }

    public function createPurchase()
    {
        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->pluck('name');

        return view('admin.inventory.purchase-create', compact('items', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:80',
            'unit' => 'required|string|max:20',
            'current_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:150',
            'status' => 'required|in:active,inactive',
        ]);

        $item = InventoryItem::create($validated);
        StockMovement::create([
            'inventory_item_id' => $item->id,
            'user_id' => $request->user()->id,
            'movement_type' => 'opening_balance',
            'quantity' => $item->current_stock,
            'stock_before' => 0,
            'stock_after' => $item->current_stock,
            'notes' => 'Opening inventory balance',
        ]);

        return redirect()->route('inventory.index')->with('message', 'Inventory item created successfully.');
    }

    public function storePurchase(Request $request)
    {
        $validated = $request->validate([
            'supplier' => 'required|string|max:150',
            'purchase_date' => 'required_without:item_id|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'invoice_reference' => 'nullable|string|max:100',
            'warehouse' => 'required_without:item_id|string|max:100',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:5000',
            'action' => 'nullable|in:draft,submit',
            'items' => 'nullable|array|min:1',
            'items.*.inventory_item_id' => 'required_with:items|exists:inventory_items,id',
            'items.*.quantity' => 'required_with:items|numeric|min:0.01',
            'items.*.unit' => 'required_with:items|string|max:30',
            'items.*.unit_cost' => 'required_with:items|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $lines = $validated['items'] ?? [];
        if ($lines === [] && $request->filled('item_id')) {
            $legacyItem = InventoryItem::findOrFail($request->input('item_id'));
            $lines = [[
                'inventory_item_id' => $legacyItem->id,
                'quantity' => $request->input('quantity'),
                'unit' => $legacyItem->unit,
                'unit_cost' => $request->input('unit_cost'),
                'tax_rate' => 0,
                'discount' => 0,
            ]];
        }

        if ($lines === []) {
            return back()->withInput()->withErrors(['items' => 'Add at least one line item.']);
        }

        $validated['purchase_date'] = $validated['purchase_date'] ?? now()->toDateString();
        $validated['warehouse'] = $validated['warehouse'] ?? 'Main Store';

        $subtotal = 0;
        $tax = 0;
        $discount = 0;
        $preparedLines = [];

        foreach ($lines as $line) {
            $lineSubtotal = (float) $line['quantity'] * (float) $line['unit_cost'];
            $lineDiscount = min((float) ($line['discount'] ?? 0), $lineSubtotal);
            $lineTax = ($lineSubtotal - $lineDiscount) * ((float) ($line['tax_rate'] ?? 0) / 100);
            $subtotal += $lineSubtotal;
            $discount += $lineDiscount;
            $tax += $lineTax;
            $preparedLines[] = array_merge($line, [
                'discount' => $lineDiscount,
                'line_total' => $lineSubtotal - $lineDiscount + $lineTax,
            ]);
        }

        $purchase = DB::transaction(function () use ($validated, $preparedLines, $subtotal, $tax, $discount, $request) {
            $status = ($request->input('action', 'draft') === 'submit') ? 'pending_approval' : 'draft';
            $supplier = Supplier::firstOrCreate(
                ['name' => $validated['supplier']],
                ['code' => 'SUP-' . strtoupper(substr(md5($validated['supplier']), 0, 8))]
            );
            $purchase = PurchaseOrder::create([
                'po_number' => 'PO-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'supplier' => $validated['supplier'],
                'supplier_id' => $supplier->id,
                'purchase_date' => $validated['purchase_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'invoice_reference' => $validated['invoice_reference'] ?? null,
                'warehouse' => $validated['warehouse'],
                'payment_terms' => $validated['payment_terms'] ?? null,
                'status' => $status,
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $subtotal - $discount + $tax,
                'notes' => $validated['notes'] ?? null,
                'item_id' => $preparedLines[0]['inventory_item_id'],
                'quantity' => $preparedLines[0]['quantity'],
                'unit_cost' => $preparedLines[0]['unit_cost'],
            ]);

            foreach ($preparedLines as $line) {
                $purchase->lines()->create([
                    'inventory_item_id' => $line['inventory_item_id'],
                    'quantity' => $line['quantity'],
                    'unit' => $line['unit'],
                    'unit_cost' => $line['unit_cost'],
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'discount' => $line['discount'],
                    'line_total' => $line['line_total'],
                ]);
            }

            PurchaseOrderStatusHistory::create([
                'purchase_order_id' => $purchase->id,
                'user_id' => $request->user()->id,
                'from_status' => null,
                'to_status' => $status,
                'action' => $status === 'draft' ? 'created_draft' : 'submitted_for_approval',
            ]);

            return $purchase;
        });

        return redirect()->route('purchases.index')->with('message', "Purchase order {$purchase->po_number} created.");
    }

    public function receiveForm(PurchaseOrder $purchase)
    {
        $purchase->load('lines.item');

        return view('admin.inventory.purchase-receive', compact('purchase'));
    }

    public function approve(PurchaseOrder $purchase)
    {
        return $this->changePurchaseStatus($purchase, 'approved', 'approved', 'Purchase order approved.');
    }

    public function reject(Request $request, PurchaseOrder $purchase)
    {
        $validated = $request->validate(['comment' => 'required|string|max:1000']);

        return $this->changePurchaseStatus($purchase, 'cancelled', 'rejected', 'Purchase order rejected.', $validated['comment']);
    }

    public function history(PurchaseOrder $purchase)
    {
        $purchase->load(['lines.item', 'statusHistory.user']);

        return view('admin.inventory.purchase-history', compact('purchase'));
    }

    private function changePurchaseStatus(PurchaseOrder $purchase, string $status, string $action, string $message, ?string $comment = null)
    {
        abort_unless(in_array($purchase->status, ['pending_approval', 'approved', 'ordered'], true), 422, 'This purchase order cannot change status now.');
        $fromStatus = $purchase->status;
        $purchase->update(['status' => $status]);
        PurchaseOrderStatusHistory::create([
            'purchase_order_id' => $purchase->id,
            'user_id' => auth()->id(),
            'from_status' => $fromStatus,
            'to_status' => $status,
            'action' => $action,
            'comment' => $comment,
        ]);

        return redirect()->route('purchases.index')->with('message', $message);
    }

    public function receive(Request $request, PurchaseOrder $purchase)
    {
        $validated = $request->validate([
            'received' => 'required|array',
            'received.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $purchase) {
            $purchase->load('lines');

            foreach ($validated['received'] as $lineId => $quantity) {
                $line = $purchase->lines->firstWhere('id', (int) $lineId);
                if (!$line || (float) $quantity === 0.0) {
                    continue;
                }

                $remaining = (float) $line->quantity - (float) $line->received_quantity;
                if ((float) $quantity > $remaining) {
                    abort(422, 'Received quantity cannot exceed the ordered quantity.');
                }

                $item = InventoryItem::whereKey($line->inventory_item_id)->lockForUpdate()->firstOrFail();
                $stockBefore = (float) $item->current_stock;
                $item->increment('current_stock', $quantity);
                $line->increment('received_quantity', $quantity);
                StockMovement::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'movement_type' => 'purchase_receipt',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore + (float) $quantity,
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $purchase->id,
                    'notes' => 'Received against ' . ($purchase->po_number ?: 'purchase order'),
                ]);
            }

            $purchase->load('lines');
            $allReceived = $purchase->lines->every(fn ($line) => (float) $line->received_quantity >= (float) $line->quantity);
            $someReceived = $purchase->lines->contains(fn ($line) => (float) $line->received_quantity > 0);
            $purchase->update(['status' => $allReceived ? 'received' : ($someReceived ? 'partially_received' : $purchase->status)]);
        });

        return redirect()->route('purchases.index')->with('message', 'Received quantities recorded and stock updated.');
    }
}
