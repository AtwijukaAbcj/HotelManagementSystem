<?php

namespace App\Http\Controllers ;

use Illuminate\Http\Request;
use App\Models\addrooms;
use App\Models\Property;
use App\Models\Stay;
use Illuminate\Support\Facades\DB;
class RoomController extends Controller
{
    //view all booking
    public function allrooms(Request $request)
    {
        $query = addrooms::query();
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        $addrooms = $query->get();
        $properties = Property::orderBy('name')->get();
        return view('admin.room.allrooms', compact('addrooms', 'properties'));
    }

    public function editrooms()
    {
        return view('admin.room.editrooms');
    }
    public function addrooms()
    {
        $properties = Property::orderBy('name')->get();

        return view('admin.room.addrooms', compact('properties'));
    }

    public function saveRoom(Request $request)
    {
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'room_number' => 'required|string|max:50|unique:addrooms,room_number',
            'floor' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        // Create a new room instance and set the values from the form
        $addrooms = new addrooms;
        $addrooms->property_id = $request->input('property_id', Property::query()->value('id'));
        $addrooms->room_type = $validated['room_type'];
        $addrooms->room_number = $validated['room_number'];
        $addrooms->floor = $validated['floor'];
        $addrooms->price = $validated['price'];
        $addrooms->operational_status = 'available';

        // Save the room to the database
        $addrooms->save();

        // Redirect to a success page or perform any other desired action
        return redirect()->to(url('/all_rooms'))->with('message', 'New Room Added Sucessfully!');
    }

    public function deleterecord1($id)
    {
        $addrooms = addrooms::find($id);
        $addrooms->delete();
        return redirect()->back()->with('message', 'Data deleted Sucessfully!');
    }





    public function updateRoomStatus(Request $request, $id)
    {
        $addrooms = addrooms::findOrFail($id);
        $addrooms->status = $request->status;
        $addrooms->save();
    
        return redirect()->back()->with('message', 'Status updated successfully');
    }

    public function statusBoard()
    {
        $rooms = addrooms::with(['stays' => function ($query) {
            $query->whereIn('status', ['reserved', 'checked_in'])
                ->whereDate('departure_date', '>=', now()->toDateString());
        }])->orderBy('room_number')->get();

        return view('admin.room.status-board', compact('rooms'));
    }

    public function roomTypes()
    {
        $roomTypes = addrooms::select(
            'room_type',
            DB::raw('COUNT(*) as total_rooms'),
            DB::raw('AVG(price) as avg_price'),
            DB::raw('MIN(price) as min_price'),
            DB::raw('MAX(price) as max_price')
        )
            ->groupBy('room_type')
            ->orderBy('room_type')
            ->get();

        return view('admin.room.types', compact('roomTypes'));
    }

    public function setOperationalStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'operational_status' => 'required|in:available,reserved,occupied,dirty,cleaning,maintenance',
        ]);

        $room = addrooms::findOrFail($id);
        $room->update($validated);

        return redirect()->back()->with('message', 'Room status updated successfully.');
    }

    public function checkIn(Request $request, $id)
    {
        $stay = Stay::findOrFail($id);
        if ($stay->status !== 'reserved') {
            return redirect()->back()->withErrors(['stay' => 'Only reserved stays can be checked in.']);
        }

        DB::transaction(function () use ($stay) {
            $stay->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
            ]);
            $stay->room->update(['operational_status' => 'occupied']);
        });

        return redirect()->back()->with('message', 'Guest checked in successfully.');
    }

    public function checkOut(Request $request, $id)
    {
        $stay = Stay::findOrFail($id);
        if ($stay->status !== 'checked_in') {
            return redirect()->back()->withErrors(['stay' => 'Only checked-in stays can be checked out.']);
        }

        DB::transaction(function () use ($stay) {
            $stay->update([
                'status' => 'checked_out',
                'checked_out_at' => now(),
            ]);
            $stay->room->update(['operational_status' => 'dirty']);
        });

        return redirect()->back()->with('message', 'Guest checked out successfully. Room marked dirty.');
    }
    
}