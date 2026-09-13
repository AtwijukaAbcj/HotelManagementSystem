<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $query = Supplier::latest();
        if ($search = request('search')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $suppliers = $query->paginate(15)->withQueryString();

        return view('admin.inventory.suppliers', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.inventory.supplier-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:suppliers,name',
            'code' => 'required|string|max:40|unique:suppliers,code',
            'contact_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('message', 'Supplier created successfully.');
    }
}