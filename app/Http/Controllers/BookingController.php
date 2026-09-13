<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Stay;
use App\Models\addrooms;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingController extends Controller
{
    //view all booking
    public function allbooking(Request $request)
    {   
        $search = $request['search'] ?? "";
        $query = Booking::query();
        if ($search != "") {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'LIKE', "%$search%")->orWhere('email_id', 'LIKE', "%$search%");
            });
        }
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        $data = $query->latest()->get();
        $properties = Property::orderBy('name')->get();

        return view('admin.booking.allbooking', compact('data', 'search', 'properties'));
    }

    public function addbooking()
    {
        $properties = Property::orderBy('name')->get();
        $roomTypes = addrooms::select('room_type')->distinct()->orderBy('room_type')->pluck('room_type');

        return view('admin.booking.addbooking', compact('properties', 'roomTypes'));
    }
    public function customers()
    {
        $data = Booking::all();
        return view('admin.customer.customer', compact('data'));
    }


    // save booking record
    public function saveRecord(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
            'room_number' => 'required|string|max:50|exists:addrooms,room_number',
            'date' => 'required|date',
            'time' => 'required',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'email_id' => 'required|email|max:255',
            'ph_number' => 'required|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        // Create a new booking instance and set the values from the form
        $property = Property::query()->first();

        $data = new Booking;
        $data->property_id = $request->input('property_id', $property ? $property->id : null);
        $data->name = $validated['name'];
        $data->room_type = $validated['room_type'];
        $data->room_number = $validated['room_number'];
        $data->date = $validated['date'];
        $data->time = $validated['time'];
        $data->arrival_date = $validated['arrival_date'];
        $data->departure_date = $validated['departure_date'];
        $data->email_id = $validated['email_id'];
        $data->ph_number = $validated['ph_number'];
        $data->message = $validated['message'] ?? null;

        DB::transaction(function () use ($data, $validated) {
            $room = addrooms::where('room_number', $data->room_number)->lockForUpdate()->first();
            $hasConflict = Stay::where('room_id', $room->id)
                ->whereIn('status', ['reserved', 'checked_in'])
                ->where('arrival_date', '<', Carbon::parse($validated['departure_date'])->toDateString())
                ->where('departure_date', '>', Carbon::parse($validated['arrival_date'])->toDateString())
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'room_number' => 'The selected room is already reserved for those dates.',
                ]);
            }

            $data->save();

            $guest = Guest::create([
                'property_id' => $data->property_id ?? ($property ?? Property::query()->first())?->id,
                'first_name' => $data->name,
                'email' => $data->email_id,
                'phone' => $data->ph_number,
            ]);

            Stay::create([
                'property_id' => $data->property_id ?? ($property ?? Property::query()->first())?->id,
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'booking_id' => $data->id,
                'arrival_date' => Carbon::parse($data->arrival_date)->toDateString(),
                'departure_date' => Carbon::parse($data->departure_date)->toDateString(),
                'status' => 'reserved',
                'nightly_rate' => $room->price,
            ]);

            $room->update(['operational_status' => 'reserved']);
        });

        // Redirect to a success page or perform any other desired action
        return redirect()->to(url('form/allbooking'))->with('message', 'New booking Added Sucessfully!');
    }
    

    public function deleterecord($id)
    {
        $data = Booking::find($id);
        $data->delete();
        return redirect()->back()->with('message', 'Data deleted Sucessfully!')->with('alert-class', 'alert-delete');
    }
    
    public function updaterecord($id)
    {
        $data = Booking::find($id);
        return view('admin.booking.editbooking', compact('data'));
    }

    public function update_data_confirm(Request $request, $id)
    {
        $data = Booking::find($id);

        // Update the booking record with the new values from the form
        $data->name = $request->input('name');
        $data->room_type = $request->input('room_type');
        $data->room_number = $request->input('room_number');
        $data->date = $request->input('date');
        $data->time = $request->input('time');
        $data->arrival_date = $request->input('arrival_date');
        $data->departure_date = $request->input('departure_date');
        $data->email_id = $request->input('email_id');
        $data->ph_number = $request->input('ph_number');
        $data->message = $request->input('message');
        // $data->boolean('status')->default(1);

        // Save the updated data to the database
        $data->save();

        // Redirect to a success page or perform any other desired action
        return redirect()->to(url('form/allbooking'))->with('message', 'Data updated successfully!');
    }



    public function updateStatus(Request $request, $id)
{
    $booking = Booking::findOrFail($id);
    $booking->status = $request->status;
    $booking->save();

    return redirect()->back()->with('message', 'Status updated successfully');
}


}
