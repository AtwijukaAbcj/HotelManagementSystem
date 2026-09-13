<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAccessControlAccess
{
    public function handle(Request $request, Closure $next, string $ability = 'view')
    {
        $user = Auth::user();
        $role = strtolower((string) ($user?->role ?? ''));
        $allowed = match ($ability) {
            'guest-cards' => in_array($role, ['admin', 'manager', 'security_manager', 'reception'], true),
            'employee-cards' => in_array($role, ['admin', 'manager', 'security_manager', 'hr'], true),
            'events', 'reports' => in_array($role, ['admin', 'manager', 'security_manager', 'auditor'], true),
            'manage' => in_array($role, ['admin', 'security_manager'], true),
            default => in_array($role, ['admin', 'manager', 'security_manager', 'reception', 'hr', 'auditor'], true),
        };

        if (!$user || !$allowed) {
            if ($user && !$request->attributes->get('access_control_denial_logged')) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'access_control.denied',
                    'new_values' => ['path' => $request->path(), 'ability' => $ability, 'role' => $role],
                    'ip_address' => $request->ip(),
                ]);
                $request->attributes->set('access_control_denial_logged', true);
            }

            abort(403, 'You do not have permission to access this security module.');
        }

        return $next($request);
    }
}
