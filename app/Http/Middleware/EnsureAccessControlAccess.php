<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAccessControlAccess
{
    public function handle(Request $request, Closure $next, string $ability = 'view')
    {
        $user = Auth::user();
        $role = strtolower((string) ($user?->role ?? ''));
        $allowed = app(ModulePermissionService::class)->can($user, 'access-control', $ability);

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
