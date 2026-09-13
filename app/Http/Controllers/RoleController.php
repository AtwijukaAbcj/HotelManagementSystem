<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use App\Models\Role;
use App\Services\ModulePermissionService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = $this->roleNames();
        $roleCounts = User::selectRaw('role, count(*) as total')->whereNotNull('role')->groupBy('role')->pluck('total', 'role');

        return view('admin.roles', compact('roles', 'roleCounts'));
    }

    public function users()
    {
        $users = User::orderBy('name')->paginate(15);
        $roles = $this->roleNames();

        return view('admin.users', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate(['role' => ['required', Rule::in($this->roleNames())]]);

        if ($user->is(auth()->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own administrator access.']);
        }

        $user->update(['role' => $validated['role'], 'usertype' => $validated['role'] === 'admin' ? '1' : '0']);

        return back()->with('message', 'User role updated successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z][a-zA-Z0-9 _-]*$/'],
        ]);
        $name = Str::of($validated['name'])->trim()->lower()->replaceMatches('/\s+/', '_')->value();

        if (Role::where('name', $name)->exists()) {
            return back()->withInput()->withErrors(['name' => 'A role with this name already exists.']);
        }

        Role::create(['name' => $name]);

        return back()->with('message', 'Role created successfully. Configure its module permissions below.');
    }

    public function permissions()
    {
        $roles = $this->roleNames();
        $modules = config('module_permissions.modules', []);
        $actions = config('module_permissions.actions', []);
        $overrides = Schema::hasTable('role_permissions')
            ? RolePermission::whereIn('role', $roles)->get()->keyBy(fn (RolePermission $permission) => "{$permission->role}.{$permission->module}.{$permission->action}")
            : collect();
        $permissions = [];

        foreach ($roles as $role) {
            foreach ($modules as $module => $label) {
                foreach ($actions as $action) {
                    $key = "{$role}.{$module}.{$action}";
                    $permissions[$role][$module][$action] = $role === 'admin'
                        || ($overrides->has($key) ? $overrides[$key]->allowed : in_array($action, config("module_permissions.defaults.{$role}.{$module}", []), true));
                }
            }
        }

        return view('admin.permissions', compact('roles', 'modules', 'actions', 'permissions'));
    }

    public function updatePermissions(Request $request, string $role, ModulePermissionService $permissionService)
    {
        abort_unless(in_array($role, $this->roleNames(), true), 404);

        if ($role === 'admin') {
            return back()->withErrors(['role' => 'Administrator access is always unrestricted.']);
        }

        $allowedPermissions = [];
        foreach (config('module_permissions.modules', []) as $module => $label) {
            foreach (config('module_permissions.actions', []) as $action) {
                $allowedPermissions[] = "{$module}.{$action}";
            }
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowedPermissions)],
        ]);

        $permissionService->setRolePermissions($role, $validated['permissions'] ?? []);

        return back()->with('message', ucfirst(str_replace('_', ' ', $role)) . ' permissions updated successfully.');
    }

    private function roleNames(): array
    {
        return Schema::hasTable('roles')
            ? Role::orderBy('name')->pluck('name')->all()
            : config('module_permissions.roles', []);
    }
}