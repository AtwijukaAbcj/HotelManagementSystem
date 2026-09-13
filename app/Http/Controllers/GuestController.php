<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::latest();
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        $guests = $query->get();
        $properties = Property::orderBy('name')->get();

        return view('admin.guest.index', compact('guests', 'properties'));
    }

    public function create()
    {
        return view('admin.guest.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Guest::create([
            'property_id' => $request->input('property_id', Property::query()->value('id')),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'id_number' => $validated['id_number'] ?? null,
            'country' => $validated['country'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('guests.index')->with('message', 'Guest profile created successfully.');
    }
}
