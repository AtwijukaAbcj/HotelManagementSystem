<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
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
        return view('admin.finance.invoice-create');
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

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'payer_name' => 'required|string|max:150',
            'reference' => 'required|string|max:100|unique:payments,reference',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $payment = Payment::create(array_merge($validated, ['status' => 'completed']));

            if ($payment->invoice_id) {
                $invoice = Invoice::lockForUpdate()->findOrFail($payment->invoice_id);
                $paid = (float) $invoice->payments()->where('status', 'completed')->sum('amount');
                $invoice->update(['status' => $paid >= (float) $invoice->amount ? 'paid' : 'pending']);
            }
        });

        return redirect()->route('payments.index')->with('message', 'Payment recorded successfully.');
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:150',
            'invoice_number' => 'required|string|max:80|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,pending,overdue',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'notes' => 'nullable|string',
        ]);

        Invoice::create($validated);

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
