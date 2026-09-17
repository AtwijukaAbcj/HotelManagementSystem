<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\InventoryItem;
use App\Models\PosOrder;
use App\Models\StockMovement;
use App\Models\addrooms;
use App\Services\ReceiptDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $items = InventoryItem::where('status', 'active')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $orders = PosOrder::where('status', 'completed')
            ->with(['items.item', 'cashier', 'guest', 'room'])
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $guests = Guest::with(['property', 'stays' => function ($query) {
            $query->with('room')->latest('id');
        }])
            ->orderBy('first_name')
            ->limit(50)
            ->get();

        $rooms = addrooms::with('property')
            ->orderBy('room_number')
            ->get();

        $categories = ['All Items', 'Food', 'Beverages', 'Snacks', 'Alcohol', 'Housekeeping', 'Other'];

        return response()
            ->view('admin.pos.index', compact('items', 'orders', 'guests', 'rooms', 'categories'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function purchases()
    {
        $orders = PosOrder::where('status', 'completed')
            ->with(['items.item', 'cashier', 'guest', 'room'])
            ->latest('updated_at')
            ->paginate(20);

        return response()
            ->view('admin.pos.purchases', compact('orders'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function receipt(PosOrder $order)
    {
        $order->refresh();
        $order->load(['items.item', 'cashier', 'guest', 'room.property', 'property']);

        $property = $order->property ?? $order->room?->property ?? \App\Models\Property::first();

        return response()
            ->view('admin.pos.receipt', compact('order', 'property'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request, ReceiptDeliveryService $receiptDelivery)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'property_id' => 'nullable|integer|exists:properties,id',
            'guest_id' => 'nullable|integer|exists:guests,id',
            'room_id' => 'nullable|integer|exists:addrooms,id',
            'table_reference' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|in:cash,card,mobile_money,room_charge',
            'amount_received' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['items'] = array_values(array_filter(
            $validated['items'],
            fn (array $line): bool => (float) $line['quantity'] > 0
        ));

        if ($validated['items'] === []) {
            return back()->withErrors(['items' => 'Select at least one item quantity.']);
        }

        $discount = (float) ($request->input('discount', 0));
        $tax = (float) ($request->input('tax', 0));
        $serviceCharge = (float) ($request->input('service_charge', 0));
        $paymentMethod = $request->input('payment_method', 'cash');
        $amountReceived = (float) ($request->input('amount_received', 0));

        $lines = [];
        $subtotal = 0;
        $createdOrder = null;

        $guest = $request->filled('guest_id') ? Guest::findOrFail($request->guest_id) : null;
        $stay = $guest?->stays()->latest('id')->first();
        $roomId = $request->input('room_id') ?: $stay?->room_id;
        $stayId = $stay && (int) $stay->room_id === (int) $roomId ? $stay->id : null;
        $room = $roomId ? addrooms::find($roomId) : null;
        $propertyId = $request->input('property_id', $room?->property_id ?? null);

        DB::transaction(function () use ($validated, $request, $guest, &$subtotal, &$lines, $discount, $tax, $serviceCharge, $paymentMethod, $amountReceived, &$createdOrder, $propertyId, $roomId, $stayId) {
            foreach ($validated['items'] as $line) {
                $item = InventoryItem::whereKey($line['id'])->lockForUpdate()->firstOrFail();

                if ($item->status !== 'active') {
                    abort(422, "{$item->name} is not active for POS sales.");
                }

                if ((float) $item->current_stock < (float) $line['quantity']) {
                    abort(422, "Insufficient stock for {$item->name}.");
                }

                $quantity = (float) $line['quantity'];
                $lineTotal = (float) $item->unit_price * $quantity;
                $subtotal += $lineTotal;

                $lines[] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $total = $subtotal - $discount + $tax + $serviceCharge;

            if ($paymentMethod === 'cash' && $amountReceived < $total) {
                abort(422, 'Cash received is below the total sale amount.');
            }

            $createdOrder = PosOrder::create([
                'user_id' => $request->user()->id,
                'reference_number' => null,
                'property_id' => $propertyId,
                'guest_id' => $request->input('guest_id'),
                'room_id' => $roomId,
                'stay_id' => $stayId,
                'receipt_email' => $guest?->email,
                'table_reference' => $request->input('table_reference'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'status' => 'completed',
                'amount_received' => $amountReceived,
                'change_due' => max(0, $amountReceived - $total),
                'notes' => $request->input('notes'),
                'completed_at' => now(),
            ]);

            $createdOrder->update([
                'reference_number' => 'POS-' . str_pad((string) $createdOrder->id, 6, '0', STR_PAD_LEFT),
            ]);

            foreach ($lines as $line) {
                $item = $line['item'];
                $quantity = $line['quantity'];
                $lineTotal = $line['line_total'];

                $createdOrder->items()->create([
                    'inventory_item_id' => $item->id,
                    'product_name_snapshot' => $item->name,
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
                    'stock_after' => $stockBefore - $quantity,
                    'reference_type' => PosOrder::class,
                    'reference_id' => $createdOrder->id,
                    'notes' => 'POS sale ' . $createdOrder->reference_number,
                ]);
            }
        });

        if (!$createdOrder) {
            return back()->withErrors(['items' => 'The sale could not be completed.']);
        }

        $createdOrder->refresh();

        $receiptDelivery->send(
            document: $createdOrder,
            email: $guest?->email,
            documentType: 'POS Receipt',
            documentNumber: $createdOrder->reference_number,
            recipientName: $guest?->full_name ?? 'Customer',
            amount: number_format((float) $createdOrder->total, 0),
            documentDate: ($createdOrder->completed_at ?? now())->format('d M Y H:i'),
            receiptUrl: route('pos.receipt', ['order' => $createdOrder]),
        );

        return redirect()->route('pos.receipt', ['order' => $createdOrder, 'print' => 1])
            ->with('message', 'Sale completed and inventory updated.');
    }
}
