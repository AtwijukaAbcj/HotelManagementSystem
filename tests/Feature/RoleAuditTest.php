<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_protected_admin_routes_require_admin_role(): void
    {
        $user = User::factory()->create([
            'usertype' => '1',
            'role' => 'reception',
        ]);

        $this->actingAs($user)->get(route('admin.roles'))->assertStatus(403);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create([
            'usertype' => '1',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->get('/audit-logs')->assertOk();
    }

    public function test_admin_can_update_a_user_role(): void
    {
        $admin = User::factory()->create(['usertype' => '1', 'role' => 'admin']);
        $staff = User::factory()->create(['usertype' => '0', 'role' => 'reception']);

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $staff), ['role' => 'finance'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'role' => 'finance',
            'usertype' => '0',
        ]);
    }

    public function test_admin_can_view_and_update_module_permissions(): void
    {
        $admin = User::factory()->create(['usertype' => '1', 'role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.roles.permissions'))->assertOk()->assertSee('Module Permissions');

        $this->actingAs($admin)->put(route('admin.roles.permissions.update', 'reception'), [
            'permissions' => ['access-control.view', 'access-control.employee-cards'],
        ])->assertRedirect();

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'reception',
            'module' => 'access-control',
            'action' => 'employee-cards',
            'allowed' => true,
        ]);
    }

    public function test_admin_can_create_a_role_for_permission_assignment(): void
    {
        $admin = User::factory()->create(['usertype' => '1', 'role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), ['name' => 'Spa Manager'])
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['name' => 'spa_manager']);
        $this->actingAs($admin)
            ->get(route('admin.roles.permissions'))
            ->assertOk()
            ->assertSee('Spa manager');
    }

    public function test_duplicate_role_names_are_rejected(): void
    {
        $admin = User::factory()->create(['usertype' => '1', 'role' => 'admin']);
        Role::create(['name' => 'spa_manager']);

        $this->actingAs($admin)
            ->from(route('admin.roles'))
            ->post(route('admin.roles.store'), ['name' => 'spa_manager'])
            ->assertRedirect(route('admin.roles'))
            ->assertSessionHasErrors('name');
    }
}
