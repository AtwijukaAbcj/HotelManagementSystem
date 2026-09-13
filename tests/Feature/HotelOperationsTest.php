<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Stay;
use App\Models\User;
use App\Models\addrooms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_routes_require_authentication(): void
    {
        $response = $this->get('/form/allbooking');

        $response->assertRedirect('/login');
    }

    public function test_booking_creates_a_guest_and_reserved_stay(): void
    {
        $user = User::factory()->create(['usertype' => '1']);
        $room = addrooms::create([
            'room_number' => '101',
            'floor' => 1,
            'price' => 120,
            'room_type' => 'Deluxe',
        ]);

        $response = $this->actingAs($user)->post('/form/savebooking', [
            'name' => 'Jordan Guest',
            'room_type' => 'Deluxe',
            'room_number' => '101',
            'date' => '2026-09-12',
            'time' => '14:00',
            'arrival_date' => '2026-10-01',
            'departure_date' => '2026-10-03',
            'email_id' => 'jordan@example.com',
            'ph_number' => '5550100',
            'message' => 'Late arrival',
        ]);

        $response->assertRedirect('/form/allbooking');
        $this->assertDatabaseHas('bookings', ['email_id' => 'jordan@example.com']);
        $this->assertDatabaseHas('guests', ['email' => 'jordan@example.com']);
        $this->assertDatabaseHas('stays', [
            'room_id' => $room->id,
            'status' => 'reserved',
        ]);
        $this->assertDatabaseHas('addrooms', [
            'id' => $room->id,
            'operational_status' => 'reserved',
        ]);
    }

    public function test_overlapping_room_booking_is_rejected(): void
    {
        $user = User::factory()->create(['usertype' => '1']);
        $room = addrooms::create([
            'room_number' => '102',
            'floor' => 1,
            'price' => 100,
            'room_type' => 'Standard',
        ]);
        $guest = Guest::create(['first_name' => 'Existing Guest']);
        Stay::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'arrival_date' => '2026-10-01',
            'departure_date' => '2026-10-03',
            'status' => 'reserved',
            'nightly_rate' => 100,
        ]);

        $response = $this->actingAs($user)->from('/form/addbooking')->post('/form/savebooking', [
            'name' => 'New Guest',
            'room_type' => 'Standard',
            'room_number' => '102',
            'date' => '2026-09-12',
            'time' => '14:00',
            'arrival_date' => '2026-10-02',
            'departure_date' => '2026-10-04',
            'email_id' => 'new@example.com',
            'ph_number' => '5550101',
        ]);

        $response->assertRedirect('/form/addbooking');
        $response->assertSessionHasErrors('room_number');
        $this->assertDatabaseMissing('bookings', ['email_id' => 'new@example.com']);
    }

    public function test_check_in_and_check_out_update_stay_and_room_state(): void
    {
        $user = User::factory()->create(['usertype' => '1']);
        $room = addrooms::create([
            'room_number' => '103',
            'floor' => 1,
            'price' => 90,
            'room_type' => 'Standard',
        ]);
        $guest = Guest::create(['first_name' => 'Staying Guest']);
        $stay = Stay::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'arrival_date' => '2026-10-05',
            'departure_date' => '2026-10-07',
            'status' => 'reserved',
            'nightly_rate' => 90,
        ]);

        $this->actingAs($user)->post(route('stays.check-in', $stay->id))->assertRedirect();
        $this->assertDatabaseHas('stays', ['id' => $stay->id, 'status' => 'checked_in']);
        $this->assertDatabaseHas('addrooms', ['id' => $room->id, 'operational_status' => 'occupied']);

        $this->actingAs($user)->post(route('stays.check-out', $stay->id))->assertRedirect();
        $this->assertDatabaseHas('stays', ['id' => $stay->id, 'status' => 'checked_out']);
        $this->assertDatabaseHas('addrooms', ['id' => $room->id, 'operational_status' => 'dirty']);
    }
}
