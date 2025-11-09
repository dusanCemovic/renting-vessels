<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Vessel;
use App\Services\Repository;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_time_conflict_with_existing_reservation(): void
    {
        // Fresh DB with a single vessel
        /** @var Vessel $vessel */
        $vessel = Vessel::factory()->create();

        // Choose a fixed local (Europe/Ljubljana) interval of 15 minutes
        $startLocal = Carbon::create(2025, 11, 8, 20, 0, 0, 'Europe/Ljubljana');
        $endLocal = $startLocal->copy()->addMinutes(15);

        // Store an existing reservation as UTC (same conversion the controller does)
        $startUtc = Repository::dateFromLocalToDB($startLocal);
        $endUtc = Repository::dateFromLocalToDB($endLocal);

        $existing = Reservation::create([
            'title' => 'Existing',
            'vessel_id' => $vessel->id,
            'start_at' => $startUtc,
            'end_at' => $endUtc,
            'required_equipment' => [],
        ]);
        $this->assertNotNull($existing);

        // Now attempt to create another reservation with the SAME local params via the HTTP endpoint
        $payload = [
            'title' => 'Overlap Attempt',
            'start_at' => $startLocal->format('Y-m-d\\TH:i'), // what the form sends (local, no tz)
            'end_at' => $endLocal->format('Y-m-d\\TH:i'),
        ];

        $response = $this->post(route('reservations.store'), $payload);
        $response->assertStatus(200);

        // Since times are normalized to UTC in the controller, this should conflict and NOT be successful
        $response->assertSee('No vessel with that equipment is available at requested time.');

        // Additionally, prove availability in UTC returns zero available vessels for that window
        $vesselReloaded = Vessel::with(['reservations', 'maintenances', 'equipment'])->findOrFail($vessel->id);
        $available = VesselReservation::checkAvailability(collect([$vesselReloaded]), $startUtc, $endUtc);
        $this->assertCount(0, $available);
    }
}
