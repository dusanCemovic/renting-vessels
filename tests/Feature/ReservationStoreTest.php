<?php

namespace Tests\Feature;

use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationStoreTest extends TestCase
{
    use RefreshDatabase;


    public function test_creates_reservation_when_vessel_available(): void
    {
        // isolate it from current date
        Carbon::setTestNow(Carbon::parse('2025-01-10 08:00:00'));

        $v1 = Vessel::factory()->create(['name' => 'Alpha', 'type' => 'TypeX', 'size' => 4]);
        $v2 = Vessel::factory()->create(['name' => 'Bravo', 'type' => 'TypeX', 'size' => 6]);

        Reservation::create([
            'title' => 'Busy Alpha',
            'vessel_id' => $v1->id,
            'start_at' => Carbon::parse('2025-01-11 09:00:00'),
            'end_at' => Carbon::parse('2025-01-11 12:00:00'),
            'required_equipment' => [],
        ]);

        // Post a reservation window where at least one vessel (v2) is available
        $payload = [
            'title' => 'My Res',
            'start_at' => '2025-01-11 13:00:00',
            'end_at' => '2025-01-11 15:00:00',
            // no required equipment
        ];

        $response = $this->post(route('reservations.store'), $payload);

        $response->assertStatus(200)
            ->assertViewIs('reserve.result')
            ->assertViewHas('success', true)
            ->assertViewHas('task');

        $this->assertDatabaseHas('reservations', [
            'title' => 'My Res',
            'start_at' => '2025-01-11 13:00:00',
            'end_at' => '2025-01-11 15:00:00',
        ]);
    }

    public function test_returns_suggestions_when_no_vessel_available(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-01-10 08:00:00'));

        $v1 = Vessel::factory()->create(['name' => 'Alpha', 'type' => 'TypeX', 'size' => 4]);
        $v2 = Vessel::factory()->create(['name' => 'Bravo', 'type' => 'TypeX', 'size' => 6]);

        // Block both vessels during the requested window (overlap ensures unavailability)
        Reservation::create([
            'title' => 'Busy Alpha',
            'vessel_id' => $v1->id,
            'start_at' => Carbon::parse('2025-01-11 12:00:00'),
            'end_at' => Carbon::parse('2025-01-11 18:00:00'),
            'required_equipment' => [],
        ]);
        Maintenance::create([
            'title' => 'Bravo Maint',
            'vessel_id' => $v2->id,
            'start_at' => Carbon::parse('2025-01-11 10:00:00'),
            'end_at' => Carbon::parse('2025-01-11 14:00:00'),

        ]);

        // Request exactly during the blocked time
        $payload = [
            'title' => 'Wanted Slot',
            'start_at' => '2025-01-11 12:30:00',
            'end_at' => '2025-01-11 13:30:00',
        ];

        $response = $this->post(route('reservations.store'), $payload);

        $response->assertStatus(200)
            ->assertViewIs('reserve.result')
            ->assertViewHas('success', false)
            ->assertViewHas('suggestions');

        $suggestions = $response->viewData('suggestions');
        $this->assertIsArray($suggestions);
        $this->assertNotEmpty($suggestions);

        // Ensure suggestions are sorted by earliest availability and are >= end of last busy interval
        $availableFroms = array_map(fn($s) => Carbon::parse($s['available_from']), $suggestions);
        $sorted = $availableFroms;
        usort($sorted, fn($a, $b) => $a <=> $b);
        $this->assertEquals($sorted, $availableFroms, 'Suggestions should be sorted by available_from asc');

        // For vessel Alpha, last busy ends at 14:00; for Bravo maintenance ends at 18:00
        foreach ($suggestions as $s) {

            if ($s['vessel_name'] === 'Alpha') {
                $this->assertTrue(Carbon::parse($s['available_from'])->lte(Carbon::parse('2025-01-11 18:00:00')));
                $this->assertFalse(Carbon::parse($s['available_from'])->lte(Carbon::parse('2025-01-11 15:00:00')));
            }

            if ($s['vessel_name'] === 'Bravo') {
                $this->assertTrue(Carbon::parse($s['available_from'])->lte(Carbon::parse('2025-01-11 14:00:00')));
                $this->assertFalse(Carbon::parse($s['available_from'])->lte(Carbon::parse('2025-01-11 13:00:00')));
            }
        }

        // check if first one is selected
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                'vessel_id' => $v2->id,
                'vessel_name' => $v2->name,
                'available_from' => Carbon::parse('2025-01-11 14:00:00')->toIso8601String(),
            ],
            $suggestions[0],
            ['vessel_id', 'vessel_name', 'available_from']
        );
    }
}
