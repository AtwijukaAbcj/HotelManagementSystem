<?php

namespace Tests\Feature;

use App\Models\AccessCard;
use App\Models\Guest;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_view_security_center_and_denial_is_audited(): void
    {
        $employee = User::factory()->create(['role' => 'staff', 'usertype' => '0']);

        $response = $this->actingAs($employee)->get(route('access-control.index'));

        $response->assertForbidden();
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $employee->id,
            'event' => 'access_control.denied',
        ]);
        $this->assertStringNotContainsString('Active cards', $response->getContent());
    }

    public function test_admin_can_view_security_center_without_hotel_sidebar_view(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'usertype' => '1']);

        $response = $this->actingAs($admin)->get(route('access-control.index'));

        $response->assertOk()->assertSee('Security &amp; Access Center', false)->assertDontSee('Dashboard');
    }

    public function test_front_desk_can_issue_guest_card_but_cannot_manage_employee_cards(): void
    {
        $frontDesk = User::factory()->create(['role' => 'reception', 'usertype' => '0']);
        $guest = Guest::create([
            'first_name' => 'Test',
            'last_name' => 'Guest',
            'email' => 'test-guest@example.com',
        ]);

        $this->actingAs($frontDesk)->post(route('access-control.guest-cards.store'), [
            'guest_id' => $guest->id,
            'card_number' => 'GUEST-TEST-001',
            'expires_at' => now()->addDay()->format('Y-m-d H:i'),
        ])->assertRedirect(route('access-control.index'));

        $this->assertDatabaseHas('access_cards', [
            'card_number' => 'GUEST-TEST-001',
            'guest_id' => $guest->id,
            'card_type' => 'guest_card',
        ]);

        $this->actingAs($frontDesk)->get(route('access-control.employee-cards.index'))->assertForbidden();
    }

    public function test_role_permission_override_controls_access_control_abilities(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'usertype' => '0']);

        $this->actingAs($manager)->get(route('access-control.access-points.index'))->assertForbidden();

        RolePermission::create([
            'role' => 'manager',
            'module' => 'access-control',
            'action' => 'manage',
            'allowed' => true,
        ]);

        $this->actingAs($manager)->get(route('access-control.access-points.index'))->assertOk();
    }
}
