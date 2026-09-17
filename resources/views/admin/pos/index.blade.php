<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Restaurant / Bar POS</title>

    @include('admin.css')
    <style>
        :root {
            --pos-bg: #f4f7fb;
            --pos-card: #ffffff;
            --pos-border: #e3e9f1;
            --pos-text: #12233f;
            --pos-muted: #718096;
            --pos-primary: #009c8c;
            --pos-primary-dark: #087c72;
            --pos-navy: #123a63;
            --pos-danger: #dc3545;
            --pos-warning: #f59e0b;
            --pos-radius: 14px;
            --pos-shadow: 0 8px 28px rgba(16, 35, 63, .07);
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--pos-bg); color: var(--pos-text); font-family: Inter, sans-serif; font-size: 13px; font-style: normal; font-weight: 400; line-height: 20px; overflow-x: hidden; }
        button, input, select { font: inherit; }
        .pos-shell { min-height: 100vh; background: var(--pos-bg); }
        .pos-topbar { height: 72px; padding: 0 22px; background: #fff; border-bottom: 1px solid var(--pos-border); display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 18px; position: sticky; top: 0; z-index: 1000; }
        .pos-brand, .pos-user-actions, .pos-center-title, .pos-product-meta, .pos-price-row, .pos-cart-head, .pos-line-head, .pos-line-actions, .pos-summary-line, .pos-recent-head { display: flex; align-items: center; }
        .pos-brand { gap: 10px; }
        .pos-brand-mark { width: 42px; height: 42px; border-radius: 11px; display: grid; place-items: center; background: #e8f7f5; color: var(--pos-primary); font-size: 20px; }
        .pos-brand-text { display: flex; flex-direction: column; line-height: 1.15; }
        .pos-brand-name { font-weight: 800; font-size: 15px; }
        .pos-brand-subtitle { color: var(--pos-muted); font-size: 11px; margin-top: 3px; }
        .pos-center-title { justify-content: center; gap: 9px; }
        .pos-center-title .title { font-size: 17px; font-weight: 800; }
        .pos-online { font-size: 11px; font-weight: 700; color: #087443; background: #e6f8f1; padding: 5px 9px; border-radius: 999px; }
        .pos-user-actions { justify-content: flex-end; gap: 8px; }
        .pos-shell-btn, .pos-dashboard-btn { border: 1px solid var(--pos-border); background: #fff; color: var(--pos-text); border-radius: 9px; padding: 9px 12px; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .pos-dashboard-btn { color: #fff; background: var(--pos-navy); border-color: var(--pos-navy); }
        .pos-user-pill { display: flex; align-items: center; gap: 8px; padding: 5px 9px 5px 5px; border: 1px solid var(--pos-border); border-radius: 999px; font-size: 12px; font-weight: 700; }
        .pos-user-pill .avatar { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 50%; background: #e8f7f5; color: var(--pos-primary-dark); }
        .pos-shell-main { padding: 18px; display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(290px, .7fr) minmax(310px, .75fr); gap: 16px; align-items: start; }
        .pos-panel { background: var(--pos-card); border: 1px solid var(--pos-border); border-radius: var(--pos-radius); box-shadow: var(--pos-shadow); }
        .pos-catalog { padding: 15px; min-width: 0; }
        .pos-toolbar { display: flex; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .pos-chip-group { display: flex; flex-wrap: wrap; gap: 7px; }
        .pos-chip { border: 1px solid var(--pos-border); background: #f8fafc; color: #526174; border-radius: 999px; padding: 7px 11px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .pos-chip.active { background: var(--pos-navy); border-color: var(--pos-navy); color: #fff; }
        .pos-search { position: relative; min-width: 210px; }
        .pos-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px; }
        .pos-search input { width: 100%; height: 38px; border: 1px solid var(--pos-border); border-radius: 9px; padding: 0 12px 0 34px; outline: 0; }
        .pos-search input:focus, .pos-inline-input:focus, .pos-select:focus { border-color: #71cfc6; box-shadow: 0 0 0 3px rgba(0,156,140,.10); }
        .pos-product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(185px, 1fr)); gap: 11px; max-height: calc(100vh - 160px); overflow-y: auto; padding-right: 3px; }
        .pos-product-card { min-height: 160px; border: 1px solid var(--pos-border); border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 10px; background: #fff; transition: .18s ease; }
        .pos-product-card:hover { transform: translateY(-2px); border-color: #b7ddd9; box-shadow: 0 7px 18px rgba(16,35,63,.08); }
        .pos-product-image { height: 54px; border-radius: 9px; display: grid; place-items: center; background: #f3f7fb; color: #60758b; font-size: 23px; overflow: hidden; }
        .pos-product-image img { width: 100%; height: 100%; object-fit: cover; }
        .pos-product-meta { justify-content: space-between; gap: 8px; align-items: flex-start; }
        .pos-product-name { margin: 0; font-size: 13px; font-weight: 800; line-height: 1.25; }
        .pos-product-category { margin-top: 3px; font-size: 10px; color: var(--pos-muted); }
        .pos-stock-badge { white-space: nowrap; font-size: 9px; font-weight: 800; color: #087443; background: #e6f8f1; border-radius: 999px; padding: 4px 6px; }
        .pos-stock-badge.low { color: #9a6700; background: #fff4d6; }
        .pos-stock-badge.out { color: #b42318; background: #feecec; }
        .pos-price-row { justify-content: space-between; margin-top: auto; }
        .pos-price { font-size: 14px; font-weight: 900; color: var(--pos-primary-dark); }
        .pos-add-btn { width: 31px; height: 31px; border: 0; border-radius: 9px; background: var(--pos-primary); color: #fff; font-size: 20px; line-height: 1; cursor: pointer; }
        .pos-add-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pos-cart, .pos-right-panel { padding: 15px; position: sticky; top: 90px; }
        .pos-cart-head { justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--pos-border); }
        .pos-cart-title { margin: 0; font-size: 15px; font-weight: 900; }
        .pos-clear-btn { border: 0; background: transparent; color: var(--pos-danger); font-size: 11px; font-weight: 800; cursor: pointer; }
        .pos-order-items { min-height: 190px; max-height: 330px; overflow-y: auto; padding: 10px 0; }
        .pos-empty-order { min-height: 170px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: var(--pos-muted); gap: 6px; }
        .pos-empty-order i { font-size: 25px; color: #a9b7c6; }
        .pos-empty-order strong { color: #455468; font-size: 12px; }
        .pos-empty-order span, .pos-empty-order p { margin: 0; font-size: 10px; }
        .pos-line-item { padding: 11px 0; border-bottom: 1px solid #edf1f5; }
        .pos-line-head, .pos-line-actions { justify-content: space-between; gap: 8px; }
        .pos-line-name { margin: 0; font-size: 12px; font-weight: 800; }
        .pos-line-price { margin-top: 2px; font-size: 10px; color: var(--pos-muted); }
        .pos-delete-btn { border: 0; background: #fff0f0; color: var(--pos-danger); width: 27px; height: 27px; border-radius: 7px; cursor: pointer; }
        .pos-line-actions { margin-top: 9px; }
        .pos-qty-controls { display: flex; align-items: center; gap: 8px; }
        .pos-qty-btn { width: 26px; height: 26px; border: 1px solid var(--pos-border); background: #f8fafc; border-radius: 7px; cursor: pointer; }
        .pos-qty-value { min-width: 16px; text-align: center; font-size: 12px; font-weight: 800; }
        .pos-line-total { font-size: 12px; font-weight: 900; }
        .pos-order-summary { border-top: 1px solid var(--pos-border); padding-top: 10px; }
        .pos-summary-line { justify-content: space-between; padding: 5px 0; font-size: 11px; color: #64748b; }
        .pos-summary-line strong { color: var(--pos-text); }
        .pos-summary-line.total { margin-top: 5px; padding-top: 10px; border-top: 1px dashed #cbd5e1; font-size: 15px; font-weight: 900; color: var(--pos-text); }
        .pos-field-row { margin-bottom: 12px; }
        .pos-field-label { display: block; margin-bottom: 5px; font-size: 10px; font-weight: 800; color: #58677a; text-transform: uppercase; letter-spacing: .04em; }
        .pos-inline-input, .pos-select { width: 100%; height: 38px; border: 1px solid var(--pos-border); border-radius: 9px; background: #fff; padding: 0 10px; color: var(--pos-text); outline: 0; font-size: 12px; }
        #guest-search { margin-bottom: 6px; }
        .pos-methods { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .pos-method { border: 1px solid var(--pos-border); background: #f8fafc; color: #526174; border-radius: 8px; padding: 8px 5px; font-size: 10px; font-weight: 800; cursor: pointer; }
        .pos-method.active { color: #fff; background: var(--pos-navy); border-color: var(--pos-navy); }
        .pos-quick-amounts { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; margin-top: 6px; }
        .pos-quick-amount { border: 1px solid var(--pos-border); background: #fff; border-radius: 7px; padding: 6px; font-size: 9px; font-weight: 700; cursor: pointer; }
        .pos-guest-box { background: #f7fafc; border: 1px solid var(--pos-border); border-radius: 9px; padding: 9px 11px; }
        .pos-guest-box strong { display: block; font-size: 15px; }
        .pos-guest-box small { color: var(--pos-muted); font-size: 10px; }
        .pos-cta-row { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; margin-top: 12px; }
        .pos-primary-btn, .pos-secondary-btn, .pos-ghost-btn { min-height: 40px; border-radius: 9px; font-size: 11px; font-weight: 900; cursor: pointer; }
        .pos-primary-btn { grid-column: 1 / -1; border: 0; color: #fff; background: var(--pos-primary); }
        .pos-secondary-btn { border: 1px solid var(--pos-navy); color: var(--pos-navy); background: #fff; }
        .pos-ghost-btn { border: 1px solid var(--pos-border); color: #64748b; background: #f8fafc; }
        .pos-receipt-preview { width: 100%; max-width: 300px; margin: 16px auto 0; padding: 15px 13px; background: #fff; border: 1px solid var(--pos-border); border-radius: 10px; color: #111; font-family: 'Courier New', monospace; font-size: 10px; }
        .receipt-header { text-align: center; padding-bottom: 8px; border-bottom: 1px dashed #aaa; }
        .receipt-logo { font-size: 18px; }
        .receipt-title { margin-top: 3px; font-size: 14px; font-weight: 900; }
        .receipt-subtitle { font-size: 9px; }
        .receipt-meta { display: grid; grid-template-columns: 72px 1fr; gap: 3px 8px; padding: 8px 0; border-bottom: 1px dashed #aaa; }
        .receipt-meta strong { text-align: right; }
        .receipt-items { padding: 7px 0; border-bottom: 1px dashed #aaa; }
        .receipt-item, .receipt-total .line { display: flex; justify-content: space-between; gap: 8px; padding: 2px 0; }
        .receipt-total { padding-top: 6px; }
        .receipt-total .grand { font-weight: 900; font-size: 12px; border-top: 1px dashed #aaa; margin-top: 4px; padding-top: 5px; }
        .pos-recent { margin: 0 18px 18px; padding: 15px; }
        .pos-recent-head { justify-content: space-between; margin-bottom: 10px; }
        .pos-recent-head h4 { margin: 0; font-size: 14px; font-weight: 900; }
        .pos-action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 76px;
            min-height: 34px;
            padding: 7px 12px;
            border-radius: 9px;
            background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);
            color: #fff;
            border: 1px solid #0f766e;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(15, 118, 110, 0.18);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .pos-action-link:hover { transform: translateY(-1px); }
        .pos-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .pos-table th { text-align: left; padding: 9px 8px; background: #f7f9fc; color: #718096; text-transform: uppercase; font-size: 9px; letter-spacing: .04em; }
        .pos-table td { padding: 10px 8px; border-top: 1px solid #edf1f5; }
        .pos-pagination { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding-top: 14px; }
        .pos-pagination-link, .pos-pagination-disabled, .pos-pagination-status { min-height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0 10px; border-radius: 8px; font-size: 10px; font-weight: 800; }
        .pos-pagination-link { border: 1px solid var(--pos-border); background: #fff; color: var(--pos-navy); text-decoration: none; }
        .pos-pagination-link:hover { border-color: #71cfc6; background: #f0fbf9; }
        .pos-pagination-disabled { border: 1px solid #edf1f5; background: #f8fafc; color: #a0aec0; }
        .pos-pagination-status { color: var(--pos-muted); }
        .status-badge { display: inline-block; border-radius: 999px; padding: 4px 8px; background: #e7f8f2; color: #087443; font-size: 9px; font-weight: 800; }
        @media (max-width: 1280px) { .pos-shell-main { grid-template-columns: minmax(0, 1.4fr) minmax(270px, .75fr); } .pos-right-panel { position: static; grid-column: 1 / -1; } .pos-right-panel form { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; } .pos-right-panel .pos-cta-row { grid-column: 1 / -1; } .pos-receipt-preview, .pos-right-panel > .no-print { display: none; } }
        @media (max-width: 900px) { .pos-topbar { grid-template-columns: 1fr auto; } .pos-center-title { display: none; } .pos-shell-main { grid-template-columns: 1fr; } .pos-cart, .pos-right-panel { position: static; } .pos-right-panel form { display: block; } .pos-shell-btn, .pos-user-pill { display: none; } .pos-product-grid { max-height: none; } }
        @media (max-width: 600px) { .pos-topbar { height: 62px; padding: 0 12px; } .pos-brand-subtitle { display: none; } .pos-dashboard-btn { padding: 8px; font-size: 0; } .pos-dashboard-btn i { font-size: 13px; } .pos-shell-main { padding: 10px; gap: 10px; } .pos-toolbar { align-items: stretch; flex-direction: column; } .pos-search { min-width: 0; } .pos-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .pos-recent { margin: 0 10px 10px; overflow-x: auto; } }
        @media print {
            @page { size: 80mm auto; margin: 0; }
            html, body { width: 80mm !important; min-width: 80mm !important; margin: 0 !important; padding: 0 !important; background: #fff !important; }
            body * { visibility: hidden !important; }
            #printReceipt, #printReceipt * { visibility: visible !important; }
            #printReceipt { position: absolute !important; left: 0 !important; top: 0 !important; width: 80mm !important; max-width: 80mm !important; margin: 0 !important; padding: 4mm !important; border: 0 !important; border-radius: 0 !important; box-shadow: none !important; background: #fff !important; color: #000 !important; font-family: 'Courier New', monospace !important; font-size: 10pt !important; }
            .receipt-header { padding-bottom: 3mm !important; }
            .receipt-meta { padding: 3mm 0 !important; grid-template-columns: 25mm 1fr !important; }
            .receipt-items { padding: 3mm 0 !important; }
            .receipt-total { padding-top: 3mm !important; }
            .receipt-item, .receipt-total .line { break-inside: avoid; page-break-inside: avoid; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="pos-shell">
    <header class="pos-topbar">
        <div class="pos-brand">
            <div class="pos-brand-mark"><i class="fas fa-hotel"></i></div>
            <div class="pos-brand-text">
                <span class="pos-brand-name">Hotel</span>
                <span class="pos-brand-subtitle">Management System</span>
            </div>
        </div>

        <div class="pos-center-title">
            <span class="title">Restaurant / Bar POS</span>
            <span class="pos-online">Online</span>
        </div>

        <div class="pos-user-actions">
            <a href="{{ route('pos.purchases') }}" class="pos-shell-btn"><i class="fas fa-receipt"></i> Receipts</a>
            <button type="button" class="pos-shell-btn"><i class="fas fa-ellipsis-v"></i> Quick Actions</button>
            <div class="pos-user-pill">
                <span class="avatar">{{ strtoupper(substr((string) auth()->user()->name, 0, 1)) }}</span>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <a href="{{ url('redirect') }}" class="pos-dashboard-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </header>

    <main class="pos-shell-main">
        <section class="pos-panel pos-catalog">
            <div class="pos-toolbar">
                <div class="pos-chip-group" aria-label="Product categories">
                    <button type="button" class="pos-chip active" data-category="All Items">All Items</button>
                    <button type="button" class="pos-chip" data-category="Food">Food</button>
                    <button type="button" class="pos-chip" data-category="Beverages">Beverages</button>
                    <button type="button" class="pos-chip" data-category="Snacks">Snacks</button>
                    <button type="button" class="pos-chip" data-category="Alcohol">Alcohol</button>
                    <button type="button" class="pos-chip" data-category="Housekeeping">Housekeeping</button>
                    <button type="button" class="pos-chip" data-category="Other">Other</button>
                </div>

                <div class="pos-search">
                    <i class="fas fa-search"></i>
                    <input id="pos-search" type="search" placeholder="Search items..." aria-label="Search items">
                </div>
            </div>

            <div class="pos-product-grid" id="pos-product-grid">
                @forelse($items as $item)
                    @php
                        $category = $item->category ?: 'Other';
                        $stock = (float) $item->current_stock;
                        $lowStock = $stock > 0 && $stock <= (float) ($item->reorder_level ?: 5);
                        $outOfStock = $stock <= 0;
                        $icon = 'fas fa-utensils';
                        if (stripos($category, 'beverage') !== false || stripos($category, 'drink') !== false) $icon = 'fas fa-mug-hot';
                        elseif (stripos($category, 'snack') !== false) $icon = 'fas fa-cookie';
                        elseif (stripos($category, 'alcohol') !== false) $icon = 'fas fa-wine-bottle';
                        elseif (stripos($category, 'house') !== false) $icon = 'fas fa-broom';
                        elseif (stripos($category, 'food') !== false) $icon = 'fas fa-burger';
                    @endphp

                    <article
                        class="pos-product-card"
                        data-category="{{ $category }}"
                        data-name="{{ strtolower($item->name) }}"
                        data-id="{{ $item->id }}"
                        data-price="{{ $item->unit_price }}"
                        data-stock="{{ $stock }}"
                    >
                        <div class="pos-product-image">
                            @if(!empty($item->image_url))
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->name }}">
                            @else
                                <i class="{{ $icon }}"></i>
                            @endif
                        </div>

                        <div class="pos-product-meta">
                            <div>
                                <h3 class="pos-product-name">{{ $item->name }}</h3>
                                <div class="pos-product-category">{{ $category }}</div>
                            </div>
                            <span class="pos-stock-badge {{ $outOfStock ? 'out' : ($lowStock ? 'low' : '') }}">
                                {{ $outOfStock ? 'Out of stock' : ($lowStock ? $stock . ' left' : $stock . ' left') }}
                            </span>
                        </div>

                        <div class="pos-price-row">
                            <div class="pos-price">UGX {{ number_format((float) $item->unit_price, 0) }}</div>
                            <button type="button" class="pos-add-btn" data-id="{{ $item->id }}" {{ $outOfStock ? 'disabled' : '' }} aria-label="Add {{ $item->name }}">+</button>
                        </div>
                    </article>
                @empty
                    <div class="pos-empty-order" style="grid-column: 1 / -1;">
                        <i class="fas fa-box-open"></i>
                        <h4>No active POS items</h4>
                        <p>Add inventory items to enable the checkout workflow.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="pos-panel pos-cart">
            <div class="pos-cart-head">
                <h3 class="pos-cart-title">Current Order</h3>
                <button type="button" id="clear-order" class="pos-clear-btn">Clear All</button>
            </div>

            <div class="pos-order-items" id="pos-order-items">
                <div class="pos-empty-order">
                    <i class="fas fa-shopping-cart"></i>
                    <strong>No items added yet.</strong>
                    <span>Select products from the catalog to begin an order.</span>
                </div>
            </div>

            <div class="pos-order-summary">
                <div class="pos-summary-line">
                    <span>Subtotal</span>
                    <strong id="summary-subtotal">UGX 0</strong>
                </div>
                <div class="pos-summary-line">
                    <span>Discount</span>
                    <strong id="summary-discount">UGX 0</strong>
                </div>
                <div class="pos-summary-line">
                    <span>Tax</span>
                    <strong id="summary-tax">UGX 0</strong>
                </div>
                <div class="pos-summary-line">
                    <span>Service Charge</span>
                    <strong id="summary-service">UGX 0</strong>
                </div>
                <div class="pos-summary-line total">
                    <span>Total</span>
                    <strong id="summary-total">UGX 0</strong>
                </div>
            </div>
        </aside>

        <aside class="pos-panel pos-right-panel">
            <form id="pos-order-form" method="POST" action="{{ route('pos.store') }}">
                @csrf

                <div class="pos-field-row">
                    <label class="pos-field-label" for="table-reference">Table / Room</label>
                    <input id="table-reference" name="table_reference" class="pos-inline-input" type="text" placeholder="Table 5 / Room 101">
                </div>

                <div class="pos-field-row">
                    <label class="pos-field-label">Customer</label>
                    <input id="guest-search" class="pos-inline-input" type="text" placeholder="Search guest by name, phone or room...">
                    <select id="guest-select" name="guest_id" class="pos-select">
                        <option value="">Walk-in customer</option>
                        @foreach($guests as $guest)
                            @php($latestStay = $guest->stays->first())
                            <option value="{{ $guest->id }}" data-name="{{ $guest->full_name }}" data-room="{{ $latestStay?->room?->room_number ?? '' }}" data-room-id="{{ $latestStay?->room_id ?? '' }}" data-stay-id="{{ $latestStay?->id ?? '' }}" data-phone="{{ $guest->phone }}">
                                {{ $guest->full_name }}{{ $guest->phone ? ' • ' . $guest->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pos-field-row">
                    <label class="pos-field-label" for="room-id">Room / Folio</label>
                    <select id="room-id" name="room_id" class="pos-select">
                        <option value="">No room selected</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="stay-id" name="stay_id" value="">
                </div>

                <div class="pos-field-row">
                    <label class="pos-field-label">Payment Method</label>
                    <div class="pos-methods">
                        <button type="button" class="pos-method active" data-payment="cash">Cash</button>
                        <button type="button" class="pos-method" data-payment="card">Card</button>
                        <button type="button" class="pos-method" data-payment="mobile_money">Mobile Money</button>
                        <button type="button" class="pos-method" data-payment="room_charge">Room Charge</button>
                    </div>
                    <input type="hidden" name="payment_method" id="payment-method" value="cash">
                </div>

                <div class="pos-field-row">
                    <label class="pos-field-label" for="amount-received">Amount Received</label>
                    <input id="amount-received" name="amount_received" class="pos-inline-input" type="number" min="0" step="1" value="0">
                    <div class="pos-quick-amounts">
                        <button type="button" class="pos-quick-amount" data-amount="0">Exact</button>
                        <button type="button" class="pos-quick-amount" data-amount="20000">UGX 20,000</button>
                        <button type="button" class="pos-quick-amount" data-amount="50000">UGX 50,000</button>
                        <button type="button" class="pos-quick-amount" data-amount="100000">UGX 100,000</button>
                    </div>
                </div>

                <div class="pos-field-row">
                    <label class="pos-field-label">Change Due</label>
                    <div class="pos-guest-box">
                        <div>
                            <strong id="change-due-display">UGX 0</strong>
                            <small id="payment-status-text">Awaiting payment</small>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="discount" id="discount-hidden" value="0">
                <input type="hidden" name="tax" id="tax-hidden" value="0">
                <input type="hidden" name="service_charge" id="service-charge-hidden" value="0">
                <input type="hidden" name="notes" value="">

                <div class="pos-cta-row">
                    <button type="submit" class="pos-primary-btn" id="complete-sale">✓ Complete Sale</button>
                    <button type="button" class="pos-secondary-btn" id="hold-order">Hold Order</button>
                    <button type="button" class="pos-ghost-btn" id="clear-order-cta">Cancel</button>
                </div>
            </form>

        </aside>
    </main>

    <section class="pos-panel" style="margin: 0 18px 18px; padding: 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap;">
            <h4 style="margin: 0; font-size: 15px; font-weight: 900;">Receipts</h4>
            <a href="{{ route('pos.purchases') }}" class="pos-shell-btn">View All Receipts</a>
        </div>

        <div class="table-responsive">
            <table class="pos-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Customer</th>
                        <th>Room / Table</th>
                        <th>Total</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->reference_number ?? '#' . $order->id }}</strong></td>
                            <td>{{ $order->guest?->full_name ?? 'Walk-in' }}</td>
                            <td>{{ $order->table_reference ?? ($order->room?->room_number ? 'Room ' . $order->room->room_number : '-') }}</td>
                            <td>UGX {{ number_format((float) $order->total, 0) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'cash')) }}</td>
                            <td>{{ $order->completed_at?->format('M d, Y H:i') ?? $order->created_at?->format('M d, Y H:i') }}</td>
                            <td><a href="{{ route('pos.receipt', ['order' => $order]) }}" class="pos-action-link">View Receipt</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--pos-muted); padding: 20px 10px;">No receipts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="pos-pagination" aria-label="Receipt pagination">
                @if($orders->onFirstPage())
                    <span class="pos-pagination-disabled">Previous</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="pos-pagination-link">Previous</a>
                @endif

                <span class="pos-pagination-status">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>

                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="pos-pagination-link">Next</a>
                @else
                    <span class="pos-pagination-disabled">Next</span>
                @endif
            </div>
        @endif
    </section>
</div>

@include('admin.script')
<script>
    const productCards = Array.from(document.querySelectorAll('.pos-product-card'));
    const cartItemsContainer = document.getElementById('pos-order-items');
    const subtotalText = document.getElementById('summary-subtotal');
    const discountText = document.getElementById('summary-discount');
    const taxText = document.getElementById('summary-tax');
    const serviceText = document.getElementById('summary-service');
    const totalText = document.getElementById('summary-total');
    const changeDisplay = document.getElementById('change-due-display');
    const paymentStatusText = document.getElementById('payment-status-text');
    const amountReceivedInput = document.getElementById('amount-received');
    const paymentMethodInput = document.getElementById('payment-method');

    const cart = {};

    function formatMoney(value) {
        return 'UGX ' + Number(value || 0).toLocaleString('en-US');
    }

    function getCartItems() {
        return Object.values(cart);
    }

    function updateTotals() {
        const items = getCartItems();
        let subtotal = 0;
        items.forEach(item => {
            subtotal += Number(item.quantity) * Number(item.price);
        });

        const discount = 0;
        const tax = 0;
        const service = 0;
        const total = subtotal - discount + tax + service;
        const received = Number(amountReceivedInput.value || 0);
        const change = Math.max(0, received - total);

        subtotalText.textContent = formatMoney(subtotal);
        discountText.textContent = formatMoney(discount);
        taxText.textContent = formatMoney(tax);
        serviceText.textContent = formatMoney(service);
        totalText.textContent = formatMoney(total);
        changeDisplay.textContent = formatMoney(change);

        if (received >= total && items.length > 0) {
            paymentStatusText.textContent = 'Payment ready';
        } else if (items.length > 0) {
            paymentStatusText.textContent = 'Awaiting payment';
        } else {
            paymentStatusText.textContent = 'No items';
        }

        document.getElementById('discount-hidden').value = discount;
        document.getElementById('tax-hidden').value = tax;
        document.getElementById('service-charge-hidden').value = service;

    }

    function renderCart() {
        const items = getCartItems();

        if (items.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="pos-empty-order">
                    <i class="fas fa-shopping-cart"></i>
                    <strong>No items added yet.</strong>
                    <span>Select products from the catalog to begin an order.</span>
                </div>
            `;
            updateTotals();
            return;
        }

        cartItemsContainer.innerHTML = items.map(item => `
            <div class="pos-line-item" data-id="${item.id}">
                <div class="pos-line-head">
                    <div>
                        <p class="pos-line-name">${item.name}</p>
                        <div class="pos-line-price">${formatMoney(item.price)} each</div>
                    </div>
                    <button type="button" class="pos-delete-btn" data-delete="${item.id}" aria-label="Remove ${item.name}"><i class="fas fa-trash"></i></button>
                </div>
                <div class="pos-line-actions">
                    <div class="pos-qty-controls">
                        <button type="button" class="pos-qty-btn" data-action="decrease" data-id="${item.id}">−</button>
                        <span class="pos-qty-value">${item.quantity}</span>
                        <button type="button" class="pos-qty-btn" data-action="increase" data-id="${item.id}">+</button>
                    </div>
                    <div class="pos-line-total">${formatMoney(item.quantity * item.price)}</div>
                </div>
            </div>
        `).join('');

        updateTotals();
    }

    function addToCart(productId) {
        const productCard = document.querySelector(`.pos-product-card[data-id="${productId}"]`);
        if (!productCard) return;

        const product = {
            id: productCard.dataset.id,
            name: productCard.querySelector('.pos-product-name').textContent.trim(),
            price: Number(productCard.dataset.price || 0),
            stock: Number(productCard.dataset.stock || 0),
        };

        if (!cart[product.id]) {
            cart[product.id] = { ...product, quantity: 0 };
        }

        if (cart[product.id].quantity >= product.stock) {
            return;
        }

        cart[product.id].quantity += 1;
        renderCart();
    }

    function changeQuantity(productId, delta) {
        if (!cart[productId]) return;
        const product = document.querySelector(`.pos-product-card[data-id="${productId}"]`);
        const stock = Number(product?.dataset.stock || cart[productId].stock || 0);
        const nextQty = cart[productId].quantity + delta;

        if (nextQty <= 0) {
            delete cart[productId];
        } else if (nextQty <= stock) {
            cart[productId].quantity = nextQty;
        }

        renderCart();
    }

    document.getElementById('clear-order').addEventListener('click', () => {
        Object.keys(cart).forEach(key => delete cart[key]);
        renderCart();
    });

    document.getElementById('clear-order-cta').addEventListener('click', () => {
        Object.keys(cart).forEach(key => delete cart[key]);
        renderCart();
    });

    document.querySelectorAll('.pos-add-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            addToCart(button.dataset.id);
        });
    });

    document.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('[data-delete]');
        if (deleteButton) {
            delete cart[deleteButton.dataset.delete];
            renderCart();
            return;
        }

        const qtyButton = event.target.closest('[data-action]');
        if (qtyButton) {
            const action = qtyButton.dataset.action;
            const productId = qtyButton.dataset.id;
            changeQuantity(productId, action === 'increase' ? 1 : -1);
        }
    });

    document.getElementById('pos-search').addEventListener('input', (event) => {
        const query = event.target.value.trim().toLowerCase();
        productCards.forEach(card => {
            const match = card.dataset.name.includes(query) || card.dataset.category.toLowerCase().includes(query);
            card.style.display = query && !match ? 'none' : 'flex';
        });
    });

    document.querySelectorAll('.pos-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.pos-chip').forEach(item => item.classList.remove('active'));
            chip.classList.add('active');
            const selected = chip.dataset.category;
            productCards.forEach(card => {
                const cardCategory = card.dataset.category || 'Other';
                const searchText = document.getElementById('pos-search').value.trim().toLowerCase();
                const matchesSearch = !searchText || card.dataset.name.includes(searchText) || card.dataset.category.toLowerCase().includes(searchText);
                card.style.display = (selected === 'All Items' || cardCategory === selected) && matchesSearch ? 'flex' : 'none';
            });
        });
    });

    document.getElementById('guest-search').addEventListener('input', (event) => {
        const query = event.target.value.trim().toLowerCase();
        const guestSelect = document.getElementById('guest-select');
        Array.from(guestSelect.options).forEach(option => {
            const text = (option.text || '').toLowerCase();
            option.hidden = query && !text.includes(query);
        });
    });

    const guestSelect = document.getElementById('guest-select');
    const roomSelect = document.getElementById('room-id');
    const stayInput = document.getElementById('stay-id');

    guestSelect.addEventListener('change', () => {
        const selectedGuest = guestSelect.options[guestSelect.selectedIndex];
        const roomId = selectedGuest?.dataset?.roomId || '';

        roomSelect.value = roomId;
        stayInput.value = roomId ? (selectedGuest.dataset.stayId || '') : '';
    });

    roomSelect.addEventListener('change', () => {
        const selectedGuest = guestSelect.options[guestSelect.selectedIndex];
        stayInput.value = selectedGuest?.dataset?.roomId === roomSelect.value
            ? (selectedGuest.dataset.stayId || '')
            : '';
    });

    document.querySelectorAll('.pos-method').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.pos-method').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            paymentMethodInput.value = button.dataset.payment;
            updateTotals();
        });
    });

    document.querySelectorAll('.pos-quick-amount').forEach(button => {
        button.addEventListener('click', () => {
            const amount = Number(button.dataset.amount || 0);
            amountReceivedInput.value = amount;
            updateTotals();
        });
    });

    amountReceivedInput.addEventListener('input', updateTotals);
    renderCart();
    updateTotals();

    const posForm = document.getElementById('pos-order-form');

    posForm.addEventListener('submit', function (event) {
        const items = getCartItems();
        if (!items.length) {
            event.preventDefault();
            document.getElementById('pos-order-items').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        const paymentMethod = paymentMethodInput.value;
        if (paymentMethod === 'cash') {
            const total = Number((document.getElementById('summary-total').textContent.replace(/[^\d.-]/g, '')) || 0);
            const received = Number(amountReceivedInput.value || 0);
            if (received < total) {
                event.preventDefault();
                alert('Cash received is below the total amount.');
                return;
            }
        }

        posForm.querySelectorAll('input[data-item-quantity]').forEach(input => input.remove());

        items.forEach(item => {
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${item.id}][id]`;
            quantityInput.value = item.id;
            quantityInput.dataset.itemQuantity = '1';
            posForm.appendChild(quantityInput);

            const qtyValueInput = document.createElement('input');
            qtyValueInput.type = 'hidden';
            qtyValueInput.name = `items[${item.id}][quantity]`;
            qtyValueInput.value = item.quantity;
            qtyValueInput.dataset.itemQuantity = '1';
            posForm.appendChild(qtyValueInput);
        });
    });
</script>
</body>
</html>
