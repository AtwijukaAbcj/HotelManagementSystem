<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Services\ReceiptDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    protected function generatePaymentReference(): string
    {
        do {
            $lastNumber = Payment::max('id') ?? 0;
            $reference = 'PAY-' . str_pad((string) ((int) $lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } while (Payment::where('reference', $reference)->exists());

        return $reference;
    }

    protected function generateInvoiceNumber(): string
    {
        do {
            $lastNumber = Invoice::max('id') ?? 0;
            $invoiceNumber = 'INV-' . str_pad((string) ((int) $lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    public function index()
    {
        $invoices = Invoice::latest()->get();
        $expenses = Expense::latest()->get();
        $revenue = $invoices->sum('amount');
        $costs = $expenses->sum('amount');
        $net = $revenue - $costs;

        return view('admin.finance.index', compact('invoices', 'expenses', 'revenue', 'costs', 'net'));
    }

    public function invoices()
    {
        $invoices = Invoice::latest()->get();
        return view('admin.finance.invoices', compact('invoices'));
    }

    public function createInvoice()
    {
        $guests = Guest::orderBy('first_name')->get();

        return view('admin.finance.invoice-create', compact('guests'));
    }

    public function printInvoice(Invoice $invoice)
    {
        return view('admin.finance.invoice-print', compact('invoice'));
    }

    public function expenses()
    {
        $expenses = Expense::latest()->get();
        return view('admin.finance.expenses', compact('expenses'));
    }

    public function createExpense()
    {
        return view('admin.finance.expense-create');
    }

    public function createPayment()
    {
        $invoices = Invoice::with('guest')->whereIn('status', ['pending', 'overdue'])->latest()->get();

        return view('admin.finance.payment-create', compact('invoices'));
    }

    public function printPayment(Payment $payment)
    {
        $payment->load('invoice');
        $property = Property::first();

        return view('admin.finance.payment-print', compact('payment', 'property'));
    }

    public function payments()
    {
        $payments = Payment::with('invoice')->latest('paid_at')->paginate(15)->withQueryString();
        $invoices = Invoice::whereIn('status', ['pending', 'overdue'])->latest()->get();
        $summary = [
            'total' => Payment::where('status', 'completed')->sum('amount'),
            'today' => Payment::where('status', 'completed')->whereDate('paid_at', today())->sum('amount'),
            'count' => Payment::count(),
        ];

        return view('admin.finance.payments', compact('payments', 'invoices', 'summary'));
    }

    public function storePayment(Request $request, ReceiptDeliveryService $receiptDelivery)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'guest_id' => 'nullable|exists:guests,id',
            'payer_name' => 'required|string|max:150',
            'payer_email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['reference'] = $this->generatePaymentReference();

        $invoice = !empty($validated['invoice_id'])
            ? Invoice::with('guest')->find($validated['invoice_id'])
            : null;
        $validated['guest_id'] = $validated['guest_id'] ?? $invoice?->guest_id;
        $validated['payer_email'] = $validated['payer_email'] ?? ($invoice?->receipt_email ?: $invoice?->guest?->email);

        $payment = DB::transaction(function () use ($validated) {
            $payment = Payment::create(array_merge($validated, ['status' => 'completed']));

            if ($payment->invoice_id) {
                $invoice = Invoice::lockForUpdate()->findOrFail($payment->invoice_id);
                $paid = (float) $invoice->payments()->where('status', 'completed')->sum('amount');
                $invoice->update(['status' => $paid >= (float) $invoice->amount ? 'paid' : 'pending']);
            }

            return $payment;
        });

        $invoice = $payment->invoice()->with('guest')->first();
        $guest = $payment->guest ?: $invoice?->guest;
        $email = $payment->payer_email ?: $guest?->email;
        $payment->forceFill(['payer_email' => $email])->saveQuietly();

        $receiptDelivery->send(
            document: $payment,
            email: $email,
            documentType: 'Payment Receipt',
            documentNumber: $payment->reference,
            recipientName: $payment->payer_name,
            amount: number_format((float) $payment->amount, 2),
            documentDate: ($payment->paid_at ?? now())->format('d M Y H:i'),
            receiptUrl: route('payments.print', ['payment' => $payment]),
            emailColumn: 'payer_email',
        );

        return redirect()->route('payments.index')->with('message', 'Payment recorded successfully.');
    }

    public function storeInvoice(Request $request, ReceiptDeliveryService $receiptDelivery)
    {
        $validated = $request->validate([
            'guest_id' => 'nullable|exists:guests,id',
            'guest_name' => 'required|string|max:150',
            'receipt_email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,pending,overdue',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'notes' => 'nullable|string',
        ]);

        $guest = !empty($validated['guest_id']) ? Guest::findOrFail($validated['guest_id']) : null;
        $validated['invoice_number'] = $this->generateInvoiceNumber();
        $validated['receipt_email'] = $validated['receipt_email'] ?: $guest?->email;
        $invoice = Invoice::create($validated);

        $receiptDelivery->send(
            document: $invoice,
            email: $invoice->receipt_email,
            documentType: 'Invoice',
            documentNumber: $invoice->invoice_number,
            recipientName: $invoice->guest_name,
            amount: number_format((float) $invoice->amount, 2),
            documentDate: $invoice->created_at->format('d M Y H:i'),
            receiptUrl: route('invoices.print', ['invoice' => $invoice]),
        );

        return redirect()->route('invoices.index')->with('message', 'Invoice created successfully.');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|max:80',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('message', 'Expense recorded successfully.');
    }
}
