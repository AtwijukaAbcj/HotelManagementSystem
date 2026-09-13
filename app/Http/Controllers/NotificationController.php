<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\Property;
use App\Services\NotificationDeliveryService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationLog::latest()->get();
        $properties = Property::query()->orderBy('name')->get();

        return view('admin.notifications.index', compact('notifications', 'properties'));
    }

    public function send(Request $request, NotificationDeliveryService $delivery)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'channel' => 'required|in:email,sms,whatsapp',
            'recipient' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $subject = $validated['subject'] ?? 'Hotel update';
        $result = $delivery->deliver($validated['channel'], $validated['recipient'], $subject, $validated['message']);

        NotificationLog::create([
            'property_id' => $validated['property_id'] ?? Property::query()->value('id'),
            'channel' => $validated['channel'],
            'recipient' => $validated['recipient'],
            'subject' => $subject,
            'message' => $validated['message'],
            'status' => $result['status'],
            'metadata' => array_merge($result, [
                'sender' => 'system',
                'created_via' => 'admin_panel',
            ]),
        ]);

        return redirect()->route('notifications.index')->with('message', "Notification status: {$result['status']}.");
    }
}
