<?php

namespace App\Http\Middleware;

use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;

class EnsureModulePermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view')
    {
        if (!app(ModulePermissionService::class)->can($request->user(), $module, $action)) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}
