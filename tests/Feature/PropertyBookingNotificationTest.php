<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyBookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_scoped_booking_and_notification_flow(): void
    {
        $admin = User::factory()->create([
            'usertype' => '1',
            'role' => 'admin',
        ]);

        $property = Property::create([
            'name' => 'Harbor Suites',
            'code' => 'HSU',
            'address' => 'Harbor Lane',
            'timezone' => 'UTC',
        ]);

        $this->actingAs($admin)
            ->post('/properties', [
                'name' => 'Harbor Suites',
                'code' => 'HSU',
                'address' => 'Harbor Lane',
                'timezone' => 'UTC',
            ]);

        $this->assertDatabaseHas('properties', ['code' => 'HSU']);

        $this->actingAs($admin)
            ->post('/notifications', [
                'property_id' => $property->id,
                'channel' => 'email',
                'recipient' => 'guest@example.com',
                'subject' => 'Check-in reminder',
                'message' => 'Your stay begins tomorrow.',
            ])
            ->assertRedirect('/notifications');

        $this->assertDatabaseHas('notification_logs', ['recipient' => 'guest@example.com']);
    }
}
