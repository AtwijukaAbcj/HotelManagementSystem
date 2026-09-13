<?php

namespace Tests\Feature;

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

        $this->actingAs($user)->get('/admin/roles')->assertStatus(403);
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
            ->put("/admin/roles/{$staff->id}", ['role' => 'finance'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'role' => 'finance',
            'usertype' => '0',
        ]);
    }
}
