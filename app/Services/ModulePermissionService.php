<?php

namespace App\Services;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ModulePermissionService
{
    public function can(?User $user, string $module, string $action = 'view'): bool
    {
        if (!$user) {
            return false;
        }

        $role = strtolower((string) $user->role);
        if ($role === 'admin') {
            return true;
        }

        if (Schema::hasTable('role_permissions')) {
            $override = RolePermission::where(compact('role', 'module', 'action'))->value('allowed');
            if ($override !== null) {
                return (bool) $override;
            }
        }

        $defaults = config("module_permissions.defaults.{$role}", []);
        $allowed = $defaults[$module] ?? [];

        return in_array($action, $allowed, true);
    }

    public function modules(?User $user): array
    {
        return collect(config('module_permissions.modules', []))
            ->filter(fn ($label, $module) => $this->can($user, $module))
            ->all();
    }

    public function setRolePermissions(string $role, array $permissions): void
    {
        foreach (config('module_permissions.modules', []) as $module => $label) {
            foreach (config('module_permissions.actions', []) as $action) {
                RolePermission::updateOrCreate(
                    compact('role', 'module', 'action'),
                    ['allowed' => in_array("{$module}.{$action}", $permissions, true)]
                );
            }
        }
    }
}
