<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiPropertyNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_property_and_view_it(): void
    {
        $admin = User::factory()->create([
            'usertype' => '1',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post('/properties', [
                'name' => 'Beachside Resort',
                'code' => 'BSR',
                'address' => 'Downtown Coast',
                'timezone' => 'UTC',
            ])
            ->assertRedirect('/properties');

        $this->assertDatabaseHas('properties', ['code' => 'BSR']);
        $this->actingAs($admin)->get('/properties')->assertOk();
    }

    public function test_admin_can_view_notification_log(): void
    {
        $admin = User::factory()->create([
            'usertype' => '1',
            'role' => 'admin',
        ]);

        Property::create([
            'name' => 'Lakeview Suites',
            'code' => 'LVS',
            'address' => 'Lakeside Drive',
            'timezone' => 'UTC',
        ]);

        $this->actingAs($admin)
            ->get('/notifications')
            ->assertOk();
    }
}
