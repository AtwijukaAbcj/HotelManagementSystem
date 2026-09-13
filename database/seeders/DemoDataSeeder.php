<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password');

        $users = [
            ['name' => 'Aarav Sharma', 'email' => 'aarav.sharma@example.com', 'phone' => '+977 9801000001', 'address' => 'Lazimpat, Kathmandu', 'join_date' => '2024-02-12', 'birth_date' => '1990-06-18', 'role' => 'Manager'],
            ['name' => 'Maya Gurung', 'email' => 'maya.gurung@example.com', 'phone' => '+977 9801000002', 'address' => 'Pokhara, Gandaki', 'join_date' => '2024-04-08', 'birth_date' => '1994-11-02', 'role' => 'Receptionist'],
            ['name' => 'Nabin Thapa', 'email' => 'nabin.thapa@example.com', 'phone' => '+977 9801000003', 'address' => 'Boudha, Kathmandu', 'join_date' => '2024-06-20', 'birth_date' => '1992-01-25', 'role' => 'Staff'],
            ['name' => 'Sita Rai', 'email' => 'sita.rai@example.com', 'phone' => '+977 9801000004', 'address' => 'Baneshwor, Kathmandu', 'join_date' => '2025-01-15', 'birth_date' => '1996-09-12', 'role' => 'Accountant'],
            ['name' => 'Prakash Karki', 'email' => 'prakash.karki@example.com', 'phone' => '+977 9801000005', 'address' => 'Bhaktapur, Nepal', 'join_date' => '2025-03-03', 'birth_date' => '1988-03-30', 'role' => 'Room Maintainer'],
            ['name' => 'Anisha Lama', 'email' => 'anisha.lama@example.com', 'phone' => '+977 9801000006', 'address' => 'Patan, Lalitpur', 'join_date' => '2025-07-22', 'birth_date' => '1997-12-08', 'role' => 'Staff'],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'usertype' => 0,
                    'password' => $password,
                    'email_verified_at' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }

        $admin = DB::table('users')->where('email', 'admin@gmail.com')->value('id');
        $staff = DB::table('users')->where('email', 'maya.gurung@example.com')->value('id');
        $accountant = DB::table('users')->where('email', 'sita.rai@example.com')->value('id');

        if (Schema::hasTable('properties')) {
            DB::table('properties')->updateOrInsert(['code' => 'KTM-01'], ['name' => 'Hotel Himalaya Kathmandu', 'address' => 'Lalitpur, Kathmandu', 'timezone' => 'Asia/Kathmandu', 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]);
            DB::table('properties')->updateOrInsert(['code' => 'PKR-01'], ['name' => 'Hotel Himalaya Lakeside', 'address' => 'Lakeside, Pokhara', 'timezone' => 'Asia/Kathmandu', 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]);
        }

        $property = Schema::hasTable('properties') ? DB::table('properties')->where('code', 'KTM-01')->value('id') : null;
        $lakeProperty = Schema::hasTable('properties') ? DB::table('properties')->where('code', 'PKR-01')->value('id') : null;

        if (Schema::hasTable('addrooms')) {
            $rooms = [
                ['room_number' => '101', 'floor' => 1, 'price' => 65, 'room_type' => 'Standard', 'status' => 1, 'operational_status' => 'available'],
                ['room_number' => '102', 'floor' => 1, 'price' => 65, 'room_type' => 'Standard', 'status' => 1, 'operational_status' => 'occupied'],
                ['room_number' => '103', 'floor' => 1, 'price' => 75, 'room_type' => 'Deluxe', 'status' => 1, 'operational_status' => 'reserved'],
                ['room_number' => '201', 'floor' => 2, 'price' => 95, 'room_type' => 'Deluxe', 'status' => 1, 'operational_status' => 'available'],
                ['room_number' => '202', 'floor' => 2, 'price' => 120, 'room_type' => 'Suite', 'status' => 1, 'operational_status' => 'maintenance'],
                ['room_number' => '203', 'floor' => 2, 'price' => 120, 'room_type' => 'Suite', 'status' => 1, 'operational_status' => 'available'],
                ['room_number' => '301', 'floor' => 3, 'price' => 150, 'room_type' => 'Executive Suite', 'status' => 1, 'operational_status' => 'available'],
                ['room_number' => '302', 'floor' => 3, 'price' => 150, 'room_type' => 'Executive Suite', 'status' => 1, 'operational_status' => 'occupied'],
            ];
            foreach ($rooms as $room) {
                DB::table('addrooms')->updateOrInsert(['room_number' => $room['room_number']], array_merge($room, ['property_id' => $property, 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('guests')) {
            $guests = [
                ['first_name' => 'Emma', 'last_name' => 'Williams', 'email' => 'emma.williams@example.com', 'phone' => '+44 7700 900101', 'id_number' => 'UK-20481', 'country' => 'United Kingdom'],
                ['first_name' => 'Daniel', 'last_name' => 'Miller', 'email' => 'daniel.miller@example.com', 'phone' => '+1 202 555 0102', 'id_number' => 'US-78312', 'country' => 'United States'],
                ['first_name' => 'Priya', 'last_name' => 'Mehta', 'email' => 'priya.mehta@example.com', 'phone' => '+91 98765 44003', 'id_number' => 'IN-44521', 'country' => 'India'],
                ['first_name' => 'Thomas', 'last_name' => 'Dubois', 'email' => 'thomas.dubois@example.com', 'phone' => '+33 6 12 34 56 04', 'id_number' => 'FR-91182', 'country' => 'France'],
                ['first_name' => 'Sofia', 'last_name' => 'Andersson', 'email' => 'sofia.andersson@example.com', 'phone' => '+46 70 123 4505', 'id_number' => 'SE-50019', 'country' => 'Sweden'],
                ['first_name' => 'Michael', 'last_name' => 'Chen', 'email' => 'michael.chen@example.com', 'phone' => '+65 8123 4506', 'id_number' => 'SG-88126', 'country' => 'Singapore'],
            ];
            foreach ($guests as $guest) {
                DB::table('guests')->updateOrInsert(['email' => $guest['email']], array_merge($guest, ['property_id' => $property, 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('bookings')) {
            $bookings = [
                ['name' => 'Emma Williams', 'room_type' => 'Deluxe', 'room_number' => '103', 'arrival_date' => now()->addDays(2)->toDateString(), 'departure_date' => now()->addDays(5)->toDateString(), 'email_id' => 'emma.williams@example.com', 'ph_number' => '+44 7700 900101', 'status' => 1],
                ['name' => 'Daniel Miller', 'room_type' => 'Standard', 'room_number' => '101', 'arrival_date' => now()->addDays(4)->toDateString(), 'departure_date' => now()->addDays(7)->toDateString(), 'email_id' => 'daniel.miller@example.com', 'ph_number' => '+1 202 555 0102', 'status' => 0],
                ['name' => 'Priya Mehta', 'room_type' => 'Suite', 'room_number' => '203', 'arrival_date' => now()->subDays(2)->toDateString(), 'departure_date' => now()->addDays(1)->toDateString(), 'email_id' => 'priya.mehta@example.com', 'ph_number' => '+91 98765 44003', 'status' => 1],
                ['name' => 'Thomas Dubois', 'room_type' => 'Executive Suite', 'room_number' => '301', 'arrival_date' => now()->addDays(8)->toDateString(), 'departure_date' => now()->addDays(12)->toDateString(), 'email_id' => 'thomas.dubois@example.com', 'ph_number' => '+33 6 12 34 56 04', 'status' => 0],
            ];
            foreach ($bookings as $booking) {
                DB::table('bookings')->updateOrInsert(['email_id' => $booking['email_id'], 'arrival_date' => $booking['arrival_date']], array_merge($booking, ['property_id' => $property, 'date' => $booking['arrival_date'], 'time' => '14:00', 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('stays') && Schema::hasTable('guests') && Schema::hasTable('addrooms')) {
            $stayGuest = DB::table('guests')->where('email', 'priya.mehta@example.com')->value('id');
            $stayRoom = DB::table('addrooms')->where('room_number', '203')->value('id');
            $booking = DB::table('bookings')->where('email_id', 'priya.mehta@example.com')->value('id');
            DB::table('stays')->updateOrInsert(['guest_id' => $stayGuest, 'room_id' => $stayRoom, 'arrival_date' => now()->subDays(2)->toDateString()], ['property_id' => $property, 'booking_id' => $booking, 'departure_date' => now()->addDays(1)->toDateString(), 'checked_in_at' => now()->subDays(2), 'status' => 'checked_in', 'nightly_rate' => 120, 'notes' => 'Demo stay', 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('employees')) {
            foreach ($users as $user) {
                DB::table('employees')->updateOrInsert(['email' => $user['email']], ['name' => $user['name'], 'phone' => $user['phone'], 'address' => $user['address'], 'join_date' => $user['join_date'], 'birth_date' => $user['birth_date'], 'role' => $user['role'], 'password' => $password, 'updated_at' => $now, 'created_at' => $now]);
            }
        }

        if (Schema::hasTable('attendences')) {
            foreach (['aarav.sharma@example.com', 'maya.gurung@example.com', 'nabin.thapa@example.com', 'sita.rai@example.com'] as $email) {
                $userId = DB::table('users')->where('email', $email)->value('id');
                DB::table('attendences')->updateOrInsert(['emp_id' => $userId, 'attendance_date' => now()->subDay()->toDateString()], ['status' => 1, 'updated_at' => $now, 'created_at' => $now]);
            }
        }

        if (Schema::hasTable('suppliers')) {
            $suppliers = [
                ['name' => 'Everest Hospitality Supply', 'code' => 'EHS-001', 'contact_name' => 'Ramesh Adhikari', 'email' => 'sales@everest-supply.test', 'phone' => '+977 9802000001', 'address' => 'Teku, Kathmandu'],
                ['name' => 'Lakeside Fresh Foods', 'code' => 'LFF-002', 'contact_name' => 'Nisha Shrestha', 'email' => 'orders@lakeside-foods.test', 'phone' => '+977 9802000002', 'address' => 'Lakeside, Pokhara'],
                ['name' => 'CleanStay Essentials', 'code' => 'CSE-003', 'contact_name' => 'Binod Joshi', 'email' => 'hello@cleanstay.test', 'phone' => '+977 9802000003', 'address' => 'Kalimati, Kathmandu'],
            ];
            foreach ($suppliers as $supplier) {
                DB::table('suppliers')->updateOrInsert(['code' => $supplier['code']], array_merge($supplier, ['status' => 'active', 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('inventory_items')) {
            $items = [
                ['name' => 'Bath Towels', 'category' => 'Housekeeping', 'unit' => 'pieces', 'current_stock' => 86, 'reorder_level' => 30, 'unit_price' => 12.50, 'supplier' => 'Everest Hospitality Supply', 'status' => 'active'],
                ['name' => 'Bed Linen Set', 'category' => 'Housekeeping', 'unit' => 'sets', 'current_stock' => 42, 'reorder_level' => 20, 'unit_price' => 28.00, 'supplier' => 'CleanStay Essentials', 'status' => 'active'],
                ['name' => 'Mineral Water 1L', 'category' => 'Beverages', 'unit' => 'bottles', 'current_stock' => 118, 'reorder_level' => 50, 'unit_price' => 1.20, 'supplier' => 'Lakeside Fresh Foods', 'status' => 'active'],
                ['name' => 'Coffee Beans', 'category' => 'Restaurant', 'unit' => 'kg', 'current_stock' => 16, 'reorder_level' => 8, 'unit_price' => 22.00, 'supplier' => 'Lakeside Fresh Foods', 'status' => 'active'],
                ['name' => 'Shampoo 30ml', 'category' => 'Amenities', 'unit' => 'boxes', 'current_stock' => 9, 'reorder_level' => 12, 'unit_price' => 18.00, 'supplier' => 'CleanStay Essentials', 'status' => 'low_stock'],
            ];
            foreach ($items as $item) {
                DB::table('inventory_items')->updateOrInsert(['name' => $item['name']], array_merge($item, ['updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('purchase_orders') && Schema::hasTable('inventory_items')) {
            $itemId = DB::table('inventory_items')->where('name', 'Shampoo 30ml')->value('id');
            $supplierId = Schema::hasTable('suppliers') ? DB::table('suppliers')->where('code', 'CSE-003')->value('id') : null;
            DB::table('purchase_orders')->updateOrInsert(['po_number' => 'PO-DEMO-001'], ['item_id' => $itemId, 'supplier' => 'CleanStay Essentials', 'supplier_id' => $supplierId, 'quantity' => 40, 'unit_cost' => 18, 'status' => 'pending', 'purchase_date' => now()->subDays(2)->toDateString(), 'expected_delivery_date' => now()->addDays(5)->toDateString(), 'invoice_reference' => 'INV-SUP-001', 'warehouse' => 'Main Store', 'payment_terms' => 'Net 30', 'payment_status' => 'unpaid', 'subtotal' => 720, 'tax' => 93.60, 'discount' => 0, 'total' => 813.60, 'notes' => 'Demo purchase order', 'updated_at' => $now, 'created_at' => $now]);
            $purchaseId = DB::table('purchase_orders')->where('po_number', 'PO-DEMO-001')->value('id');
            if (Schema::hasTable('purchase_order_items')) {
                DB::table('purchase_order_items')->updateOrInsert(['purchase_order_id' => $purchaseId, 'inventory_item_id' => $itemId], ['quantity' => 40, 'received_quantity' => 0, 'unit' => 'boxes', 'unit_cost' => 18, 'tax_rate' => 13, 'discount' => 0, 'line_total' => 720, 'updated_at' => $now, 'created_at' => $now]);
            }
            if (Schema::hasTable('purchase_order_status_histories')) {
                DB::table('purchase_order_status_histories')->updateOrInsert(['purchase_order_id' => $purchaseId, 'to_status' => 'pending', 'action' => 'created'], ['user_id' => $admin, 'from_status' => null, 'comment' => 'Demo purchase order created', 'updated_at' => $now, 'created_at' => $now]);
            }
        }

        if (Schema::hasTable('stock_movements') && Schema::hasTable('inventory_items')) {
            $waterId = DB::table('inventory_items')->where('name', 'Mineral Water 1L')->value('id');
            DB::table('stock_movements')->updateOrInsert(['inventory_item_id' => $waterId, 'movement_type' => 'receipt', 'reference_type' => 'demo'], ['user_id' => $admin, 'quantity' => 120, 'stock_before' => 0, 'stock_after' => 120, 'reference_id' => 1, 'notes' => 'Demo opening stock', 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('invoices')) {
            $invoices = [
                ['guest_name' => 'Priya Mehta', 'invoice_number' => 'INV-DEMO-001', 'amount' => 360, 'status' => 'paid', 'payment_method' => 'card'],
                ['guest_name' => 'Emma Williams', 'invoice_number' => 'INV-DEMO-002', 'amount' => 285, 'status' => 'pending', 'payment_method' => null],
                ['guest_name' => 'Daniel Miller', 'invoice_number' => 'INV-DEMO-003', 'amount' => 195, 'status' => 'paid', 'payment_method' => 'cash'],
            ];
            foreach ($invoices as $invoice) {
                DB::table('invoices')->updateOrInsert(['invoice_number' => $invoice['invoice_number']], array_merge($invoice, ['notes' => 'Demo invoice', 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('payments')) {
            $invoiceId = Schema::hasTable('invoices') ? DB::table('invoices')->where('invoice_number', 'INV-DEMO-001')->value('id') : null;
            DB::table('payments')->updateOrInsert(['reference' => 'PAY-DEMO-001'], ['invoice_id' => $invoiceId, 'payer_name' => 'Priya Mehta', 'amount' => 360, 'method' => 'card', 'status' => 'completed', 'paid_at' => now()->subDays(1), 'notes' => 'Demo payment', 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('expenses')) {
            $expenses = [
                ['title' => 'Electricity bill', 'category' => 'Utilities', 'amount' => 420, 'payment_method' => 'Bank transfer'],
                ['title' => 'Laundry service', 'category' => 'Operations', 'amount' => 185, 'payment_method' => 'Card'],
                ['title' => 'Staff refreshments', 'category' => 'Staff welfare', 'amount' => 75, 'payment_method' => 'Cash'],
            ];
            foreach ($expenses as $expense) {
                DB::table('expenses')->updateOrInsert(['title' => $expense['title']], array_merge($expense, ['notes' => 'Demo expense', 'updated_at' => $now, 'created_at' => $now]));
            }
        }

        if (Schema::hasTable('pos_orders') && Schema::hasTable('inventory_items')) {
            $coffeeId = DB::table('inventory_items')->where('name', 'Coffee Beans')->value('id');
            $orderId = DB::table('pos_orders')->insertGetId(['user_id' => $staff, 'total' => 18.50, 'payment_status' => 'paid', 'status' => 'completed', 'created_at' => $now->copy()->subHours(4), 'updated_at' => $now->copy()->subHours(4)]);
            if (Schema::hasTable('pos_order_items')) {
                DB::table('pos_order_items')->insert(['pos_order_id' => $orderId, 'inventory_item_id' => $coffeeId, 'quantity' => 1, 'unit_price' => 18.50, 'line_total' => 18.50, 'created_at' => $now->copy()->subHours(4), 'updated_at' => $now->copy()->subHours(4)]);
            }
        }

        if (Schema::hasTable('notification_logs')) {
            DB::table('notification_logs')->updateOrInsert(['subject' => 'Demo welcome message', 'recipient' => 'all-staff'], ['property_id' => $property, 'channel' => 'dashboard', 'message' => 'Welcome to the Hotel Himalaya operations dashboard.', 'status' => 'sent', 'metadata' => json_encode(['demo' => true]), 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('calanders')) {
            DB::table('calanders')->updateOrInsert(['event_name' => 'Monthly staff briefing'], ['event_date' => now()->addDays(3)->setTime(10, 0), 'updated_at' => $now, 'created_at' => $now]);
            DB::table('calanders')->updateOrInsert(['event_name' => 'Quarterly fire safety inspection'], ['event_date' => now()->addDays(10)->setTime(15, 0), 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('billings')) {
            DB::table('billings')->updateOrInsert(['name' => 'Priya Mehta', 'billing_date' => now()->subDays(1)->toDateString()], ['room_type' => 'Suite', 'room_number' => '203', 'billing_time' => '11:30', 'no_of_days_stay' => 3, 'price' => 120, 'total' => 360, 'transaction_type' => 'Card', 'updated_at' => $now, 'created_at' => $now]);
        }

        if (Schema::hasTable('audit_logs')) {
            DB::table('audit_logs')->updateOrInsert(['event' => 'demo.seeded', 'user_id' => $admin], ['property_id' => $property, 'old_values' => null, 'new_values' => json_encode(['source' => 'DemoDataSeeder']), 'ip_address' => '127.0.0.1', 'updated_at' => $now, 'created_at' => $now]);
        }
    }
}
