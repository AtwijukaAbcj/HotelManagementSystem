<?php

namespace App\Http\Controllers;

use App\Models\AccessCard;
use App\Models\AccessLog;
use App\Models\AccessPoint;
use App\Models\Guest;
use App\Models\RestrictedArea;
use App\Models\SecurityAlert;
use App\Models\Stay;
use App\Models\User;
use App\Models\addrooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessControlController extends Controller
{
    public function index()
    {
        $summary = [
            'active_cards' => AccessCard::where('status', 'active')->count(),
            'denied_today' => AccessLog::where('result', 'denied')->whereDate('accessed_at', today())->count(),
            'unresolved_alerts' => SecurityAlert::whereIn('status', ['open', 'investigating'])->count(),
            'offline_points' => AccessPoint::where(function ($query) { $query->where('status', '!=', 'online')->orWhereNull('last_communication')->orWhere('last_communication', '<', now()->subMinutes(10)); })->count(),
        ];
        $modules = [
            ['key' => 'guest-cards', 'icon' => 'fa-id-card', 'title' => 'Guest Access Cards', 'description' => 'Issue, extend, replace, suspend, and revoke guest cards.', 'count' => AccessCard::where('card_type', 'guest_card')->where('status', 'active')->count(), 'label' => 'active cards', 'route' => route('access-control.guest-cards.index'), 'ability' => 'guest-cards'],
            ['key' => 'employee-cards', 'icon' => 'fa-key', 'title' => 'Employee Mastercards', 'description' => 'Manage staff credentials and permitted work areas.', 'count' => AccessCard::where('card_type', 'employee_mastercard')->where('status', 'active')->count(), 'label' => 'active cards', 'route' => route('access-control.employee-cards.index'), 'ability' => 'employee-cards'],
            ['key' => 'events', 'icon' => 'fa-wave-square', 'title' => 'Access Events', 'description' => 'Review entry, exit, and denied access records.', 'count' => AccessLog::whereDate('accessed_at', today())->count(), 'label' => 'events today', 'route' => route('access-control.events.index'), 'ability' => 'events'],
            ['key' => 'access-points', 'icon' => 'fa-door-open', 'title' => 'Access Points and Readers', 'description' => 'Monitor doors, gates, elevators, and readers.', 'count' => AccessPoint::where('status', 'online')->count(), 'label' => 'online readers', 'route' => route('access-control.access-points.index'), 'ability' => 'manage'],
            ['key' => 'restricted-areas', 'icon' => 'fa-lock', 'title' => 'Restricted Areas', 'description' => 'Configure secure hotel areas and policies.', 'count' => RestrictedArea::where('is_active', true)->count(), 'label' => 'controlled areas', 'route' => route('access-control.restricted-areas.index'), 'ability' => 'manage'],
            ['key' => 'alerts', 'icon' => 'fa-triangle-exclamation', 'title' => 'Security Alerts', 'description' => 'Review denied access and suspicious activity.', 'count' => $summary['unresolved_alerts'], 'label' => 'unresolved alerts', 'route' => route('access-control.alerts.index'), 'ability' => 'events'],
            ['key' => 'lost-cards', 'icon' => 'fa-ban', 'title' => 'Lost or Stolen Cards', 'description' => 'Block compromised cards immediately.', 'count' => AccessCard::whereIn('status', ['blocked', 'revoked'])->count(), 'label' => 'blocked cards', 'route' => route('access-control.guest-cards.index', ['status' => 'blocked']), 'ability' => 'guest-cards'],
            ['key' => 'reports', 'icon' => 'fa-chart-line', 'title' => 'Access Reports', 'description' => 'Generate security and card activity reports.', 'count' => null, 'label' => 'Open reports', 'route' => route('access-control.reports.index'), 'ability' => 'reports'],
            ['key' => 'settings', 'icon' => 'fa-sliders', 'title' => 'Access Control Settings', 'description' => 'Configure expiry, access rules, and policies.', 'count' => null, 'label' => 'Configure', 'route' => route('access-control.settings.index'), 'ability' => 'manage'],
        ];
        $role = strtolower((string) auth()->user()->role);
        $modules = array_values(array_filter($modules, function (array $module) use ($role): bool {
            return match ($module['ability']) {
                'guest-cards' => in_array($role, ['admin', 'manager', 'security_manager', 'reception'], true),
                'employee-cards' => in_array($role, ['admin', 'manager', 'security_manager', 'hr'], true),
                'events', 'reports' => in_array($role, ['admin', 'manager', 'security_manager', 'auditor'], true),
                'manage' => in_array($role, ['admin', 'security_manager'], true),
                default => false,
            };
        }));
        $recentAlerts = SecurityAlert::with(['card', 'assignee'])->latest()->limit(5)->get();
        $recentEvents = AccessLog::with(['card', 'guest', 'user', 'room'])->latest('accessed_at')->limit(8)->get();

        return view('access-control.index', compact('summary', 'modules', 'recentAlerts', 'recentEvents'));
    }

    public function guestCards(Request $request)
    {
        $cards = AccessCard::with(['guest', 'user'])->where('card_type', 'guest_card')->when($request->filled('search'), fn ($q) => $q->where('card_number', 'like', '%' . $request->search . '%')->orWhereHas('guest', fn ($guest) => $guest->where('first_name', 'like', '%' . $request->search . '%')->orWhere('last_name', 'like', '%' . $request->search . '%')))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->latest()->paginate(15)->withQueryString();
        return view('access-control.guest-cards.index', compact('cards'));
    }

    public function createGuestCard() { return view('access-control.guest-cards.create', ['guests' => Guest::orderBy('first_name')->get(), 'rooms' => addrooms::orderBy('room_number')->get()]); }

    public function employeeCards(Request $request)
    {
        $cards = AccessCard::with('user')->where('card_type', 'employee_mastercard')->when($request->filled('search'), fn ($q) => $q->where('card_number', 'like', '%' . $request->search . '%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%' . $request->search . '%')))->latest()->paginate(15)->withQueryString();
        return view('access-control.employee-cards.index', compact('cards'));
    }

    public function createEmployeeCard() { return view('access-control.employee-cards.create', ['employees' => User::where('role', '!=', 'admin')->orderBy('name')->get()]); }

    public function events(Request $request)
    {
        $events = AccessLog::with(['card', 'guest', 'user', 'room'])->when($request->filled('result'), fn ($q) => $q->where('result', $request->result))->when($request->filled('event_type'), fn ($q) => $q->where('event_type', $request->event_type))->when($request->filled('date'), fn ($q) => $q->whereDate('accessed_at', $request->date))->latest('accessed_at')->paginate(20)->withQueryString();
        return view('access-control.events.index', compact('events'));
    }

    public function accessPoints() { return view('access-control.access-points.index', ['points' => AccessPoint::latest()->paginate(15)]); }
    public function storeAccessPoint(Request $request) { $data = $request->validate(['name' => 'required|string|max:120', 'area' => 'required|string|max:120', 'device_type' => 'required|string|max:80']); AccessPoint::create(array_merge($data, ['last_communication' => now()])); return back()->with('message', 'Access point created.'); }
    public function restrictedAreas() { return view('access-control.restricted-areas.index', ['areas' => RestrictedArea::latest()->get()]); }
    public function storeRestrictedArea(Request $request) { $data = $request->validate(['name' => 'required|string|max:120', 'description' => 'nullable|string|max:500', 'severity' => 'required|in:warning,high,critical']); RestrictedArea::create($data); return back()->with('message', 'Restricted area created.'); }
    public function reports() { return view('access-control.reports.index', ['eventCount' => AccessLog::count(), 'grantedCount' => AccessLog::where('result', 'granted')->count(), 'deniedCount' => AccessLog::where('result', 'denied')->count(), 'cardCount' => AccessCard::count()]); }
    public function settings() { return view('access-control.settings.index'); }
    public function alerts() { return view('access-control.alerts.index', ['alerts' => SecurityAlert::with(['card', 'assignee'])->latest()->paginate(15)]); }
    public function updateAlert(Request $request, SecurityAlert $alert) { $data = $request->validate(['status' => 'required|in:open,investigating,resolved', 'resolution_notes' => 'nullable|string|max:2000']); $alert->update(array_merge($data, ['resolved_at' => $data['status'] === 'resolved' ? now() : null])); return back()->with('message', 'Security alert updated.'); }

    public function issueGuestCard(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'card_number' => 'required|string|max:80|unique:access_cards,card_number',
            'expires_at' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            AccessCard::create([
            'card_number' => $validated['card_number'],
            'card_type' => 'guest_card',
            'guest_id' => $validated['guest_id'],
            'status' => 'active',
            'issued_at' => now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('access-control.index')->with('message', 'Guest access card issued successfully.');
    }

    public function issueEmployeeCard(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_number' => 'required|string|max:80|unique:access_cards,card_number',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            AccessCard::create([
            'card_number' => $validated['card_number'],
            'card_type' => 'employee_mastercard',
            'user_id' => $validated['user_id'],
            'status' => 'active',
            'issued_at' => now(),
            'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('access-control.index')->with('message', 'Employee Mastercard issued successfully.');
    }

    public function revokeCard(AccessCard $card)
    {
        DB::transaction(fn () => $card->update(['status' => 'revoked']));

        return redirect()->route('access-control.index')->with('message', 'Access card revoked.');
    }

    public function activateCard(AccessCard $card) { $card->update(['status' => 'active']); return back()->with('message', 'Card activated.'); }
    public function suspendCard(AccessCard $card) { $card->update(['status' => 'suspended']); return back()->with('message', 'Card suspended.'); }
    public function replaceCard(Request $request, AccessCard $card) { $data = $request->validate(['card_number' => 'required|string|max:80|unique:access_cards,card_number']); DB::transaction(function () use ($card, $data) { $card->update(['status' => 'replaced']); AccessCard::create(['card_number' => $data['card_number'], 'card_type' => $card->card_type, 'guest_id' => $card->guest_id, 'user_id' => $card->user_id, 'department' => $card->department, 'status' => 'active', 'issued_at' => now(), 'expires_at' => $card->expires_at, 'permitted_areas' => $card->permitted_areas, 'replaced_card_id' => $card->id]); }); return back()->with('message', 'Card replaced.'); }

    public function recordAccess(Request $request)
    {
        $validated = $request->validate([
            'access_card_id' => 'required|exists:access_cards,id',
            'access_point' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'event_type' => 'required|in:entry,exit',
            'room_id' => 'nullable|exists:addrooms,id',
        ]);

        $card = AccessCard::with(['guest', 'user'])->findOrFail($validated['access_card_id']);
        $isUsable = $card->status === 'active'
            && (!$card->expires_at || $card->expires_at->isFuture());
        $isRestricted = in_array(strtolower($validated['area']), ['control room', 'server room', 'staff only'], true);
        $hasRoomAccess = $card->card_type === 'employee_mastercard';
        if ($card->card_type === 'guest_card' && strtolower($validated['area']) === 'guest room') {
            $hasRoomAccess = $validated['room_id'] && Stay::where('guest_id', $card->guest_id)
                ->where('room_id', $validated['room_id'])
                ->whereIn('status', ['reserved', 'checked_in'])
                ->whereDate('departure_date', '>=', today())
                ->exists();
        }
        $isAuthorized = $card->card_type === 'employee_mastercard'
            || (!$isRestricted && (strtolower($validated['area']) !== 'guest room' || $hasRoomAccess));
        $result = $isUsable && $isAuthorized ? 'granted' : 'denied';

        $accessLog = AccessLog::create([
            'access_card_id' => $card->id,
            'guest_id' => $card->guest_id,
            'user_id' => $card->user_id,
            'room_id' => $validated['room_id'] ?? null,
            'access_point' => $validated['access_point'],
            'area' => $validated['area'],
            'event_type' => $validated['event_type'],
            'result' => $result,
            'reason' => $result === 'granted' ? null : 'Card inactive, expired, or not authorized for this area.',
            'accessed_at' => now(),
        ]);

        if ($result === 'denied') {
            SecurityAlert::firstOrCreate([
                'access_log_id' => $accessLog->id,
            ], [
                'alert_type' => 'denied_access',
                'severity' => 'warning',
                'access_card_id' => $card->id,
                'user_id' => $card->user_id,
                'location' => $validated['area'],
                'status' => 'open',
                'description' => 'Access denied for ' . $card->card_number,
            ]);
        }

        return redirect()->route('access-control.index')->with(
            $result === 'granted' ? 'message' : 'error',
            $result === 'granted' ? 'Access granted and logged.' : 'Access denied and logged.'
        );
    }
}
