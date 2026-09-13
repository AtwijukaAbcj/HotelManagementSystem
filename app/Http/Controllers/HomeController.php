<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\addrooms;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function redirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usertype = Auth::user()->usertype;

        if ($usertype == '1') {
            $lastBooking = Booking::latest()->first();
            $lastBookingId = $lastBooking ? $lastBooking->id : 0;

            $lastRoom = addrooms::latest()->first();
            $lastRoomId = $lastRoom ? $lastRoom->id : 0;

            $data = Booking::latest()->take(5)->get();
            $lastEmp = User::latest()->first();
            $lastEmpId = $lastEmp ? $lastEmp->id : 0;

            $stats = [
                'total_rooms' => addrooms::count(),
                'available' => addrooms::where('operational_status', 'available')->count(),
                'occupied' => addrooms::where('operational_status', 'occupied')->count(),
                'reserved' => addrooms::where('operational_status', 'reserved')->count(),
                'dirty' => addrooms::where('operational_status', 'dirty')->count(),
                'maintenance' => addrooms::where('operational_status', 'maintenance')->count(),
            ];

            $recentGuests = Guest::latest()->take(5)->get();

            $roomTypeSummary = addrooms::select(
                'room_type',
                DB::raw('COUNT(*) as total_rooms'),
                DB::raw('AVG(price) as avg_price'),
                DB::raw('MIN(price) as min_price'),
                DB::raw('MAX(price) as max_price')
            )
                ->groupBy('room_type')
                ->orderBy('room_type')
                ->get();

            return view('admin.home', compact('lastBookingId', 'lastRoomId', 'lastEmpId', 'data', 'stats', 'recentGuests', 'roomTypeSummary'));

        } else {
            return view('employee.home');
        }
    }
}
