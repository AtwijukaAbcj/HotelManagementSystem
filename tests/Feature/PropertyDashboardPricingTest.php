<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Models\addrooms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyDashboardPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_dashboard_shows_room_pricing_summary(): void
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

        addrooms::create([
            'property_id' => $property->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 120.00,
            'room_type' => 'Deluxe',
            'operational_status' => 'available',
        ]);

        addrooms::create([
            'property_id' => $property->id,
            'room_number' => '102',
            'floor' => 1,
            'price' => 150.00,
            'room_type' => 'Deluxe',
            'operational_status' => 'occupied',
        ]);

        $this->actingAs($admin)
            ->get('/properties/dashboard')
            ->assertOk()
            ->assertSee('Deluxe')
            ->assertSee('120');
    }
}
