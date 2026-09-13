<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_routes_require_authentication(): void
    {
        $this->get('/finance')->assertRedirect('/login');
        $this->get('/payments')->assertRedirect('/login');
        $this->get('/invoices')->assertRedirect('/login');
        $this->get('/expenses')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_invoice_and_expense_records(): void
    {
        $user = User::factory()->create(['usertype' => '1']);

        $invoiceResponse = $this->actingAs($user)->post('/invoices', [
            'guest_name' => 'Finance Guest',
            'invoice_number' => 'INV-1001',
            'amount' => 320.00,
            'status' => 'paid',
            'payment_method' => 'cash',
            'notes' => 'Stay settlement',
        ]);

        $invoiceResponse->assertRedirect('/invoices');
        $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV-1001']);

        $expenseResponse = $this->actingAs($user)->post('/expenses', [
            'title' => 'Laundry service',
            'category' => 'Operations',
            'amount' => 45.50,
            'payment_method' => 'cash',
            'notes' => 'Guest laundry',
        ]);

        $expenseResponse->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', ['title' => 'Laundry service']);
    }

    public function test_payment_is_recorded_and_marks_invoice_paid(): void
    {
        $user = User::factory()->create(['usertype' => '1']);

        $this->actingAs($user)->post('/invoices', [
            'guest_name' => 'Payment Guest',
            'invoice_number' => 'INV-PAY-1',
            'amount' => 50000,
            'status' => 'pending',
            'payment_method' => 'cash',
        ]);

        $invoice = \App\Models\Invoice::where('invoice_number', 'INV-PAY-1')->firstOrFail();

        $this->actingAs($user)->post('/payments', [
            'invoice_id' => $invoice->id,
            'payer_name' => 'Payment Guest',
            'reference' => 'PMT-1',
            'amount' => 50000,
            'method' => 'mobile_money',
            'paid_at' => '2026-09-12 10:00:00',
        ])->assertRedirect('/payments');

        $this->assertDatabaseHas('payments', ['reference' => 'PMT-1', 'amount' => 50000]);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }
}
