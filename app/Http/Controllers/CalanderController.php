<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calander;

class CalanderController extends Controller
{
    public function calander()
    {
        $events = Calander::orderBy('event_date')->get();
        $calendarEvents = $events->map(function (Calander $event) {
            return [
                'title' => $event->event_name,
                'start' => $event->event_date->format('Y-m-d'),
            ];
        })->values()->all();
        $upcomingEvents = $events->filter(fn (Calander $event) => $event->event_date->isToday() || $event->event_date->isFuture())->take(6);
        $calendarStats = [
            'total' => $events->count(),
            'today' => $events->filter(fn (Calander $event) => $event->event_date->isToday())->count(),
            'upcoming' => $upcomingEvents->count(),
        ];

        return view('admin.calander', compact('calendarEvents', 'upcomingEvents', 'calendarStats'));
    }

    public function createEvent(Request $request)
    {

        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date_format:Y-m-d',
        ]);

        
        // Assuming you have an Event model named 'Calander'
        Calander::create([
            'event_name' => $request->input('event_name'),
            'event_date' => $request->input('event_date'),
        ]);
    
        return redirect()->back()->with('message', 'Event added successfully!');
    }
    
}
