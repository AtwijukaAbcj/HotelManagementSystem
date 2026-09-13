<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\MaintenanceRequest;
use App\Models\addrooms;
use App\Models\User;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function housekeeping()
    {
        $tasks = HousekeepingTask::with('room')->latest()->get();
        $rooms = addrooms::orderBy('room_number')->get();
        $employees = User::where('role', '!=', 'admin')->orderBy('name')->get(['name']);

        return view('admin.operations.housekeeping', compact('tasks', 'rooms', 'employees'));
    }

    public function maintenance()
    {
        $requests = MaintenanceRequest::with('room')->latest()->get();
        $rooms = addrooms::orderBy('room_number')->get();
        $employees = User::where('role', '!=', 'admin')->orderBy('name')->get(['name']);

        return view('admin.operations.maintenance', compact('requests', 'rooms', 'employees'));
    }

    public function storeHousekeeping(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:addrooms,id',
            'task_type' => 'required|string|max:100',
            'assignee' => 'required|string|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        HousekeepingTask::create($validated);

        return redirect()->route('housekeeping.index')->with('message', 'Housekeeping assignment saved successfully.');
    }

    public function storeMaintenance(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:addrooms,id',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:open,in_progress,resolved',
            'requested_by' => 'nullable|string|max:100',
            'assigned_to' => 'nullable|string|max:100',
        ]);

        MaintenanceRequest::create($validated);

        return redirect()->route('maintenance.index')->with('message', 'Maintenance request submitted successfully.');
    }
}
