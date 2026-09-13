<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\addrooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->get();

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:properties,code',
            'address' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:80',
        ]);

        Property::create($validated);

        return redirect()->route('properties.index')->with('message', 'Property created successfully.');
    }

    public function dashboard()
    {
        $properties = Property::withCount('rooms')->get();

        $propertySummaries = [];

        foreach ($properties as $property) {
            $rooms = addrooms::where('property_id', $property->id)
                ->select(
                    'room_type',
                    DB::raw('COUNT(*) as total_rooms'),
                    DB::raw('AVG(price) as avg_price'),
                    DB::raw('MIN(price) as min_price'),
                    DB::raw('MAX(price) as max_price'),
                    DB::raw('SUM(CASE WHEN operational_status = "occupied" THEN 1 ELSE 0 END) as occupied_rooms'),
                    DB::raw('SUM(CASE WHEN operational_status = "available" THEN 1 ELSE 0 END) as available_rooms')
                )
                ->groupBy('room_type')
                ->get();

            $propertySummaries[] = [
                'property' => $property,
                'rooms' => $rooms,
                'total_rooms' => $property->rooms_count,
                'occupied_rooms' => $rooms->sum('occupied_rooms'),
                'available_rooms' => $rooms->sum('available_rooms'),
            ];
        }

        return view('admin.properties.dashboard', compact('propertySummaries'));
    }
}
