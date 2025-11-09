<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Vessel;
use App\Services\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationVesselSixTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_vessel_6_is_reserved_first_and_then_suggested_on_repeat(): void
    {
        // We can try to seed the current database fixtures (equipment, vessels, reservations, maintenances)
        // $this->seed();

        // Ensure the required equipment codes exist (A1, A2)
        $a1 = Equipment::firstOrCreate(['code' => 'A1'], ['name' => 'AddOn A1']);
        $a2 = Equipment::firstOrCreate(['code' => 'A2'], ['name' => 'AddOn A2']);

        // Make sure we have at least 5 seeded vessels; if not, create up to 5
        $existingCount = Vessel::count();
        if ($existingCount < 5) {
            Vessel::factory()->count(5 - $existingCount)->create();
        }

        // Reconfigure the first five vessels so none of them have BOTH A1 and A2
        $vessels = Vessel::orderBy('id')->take(5)->get();
        foreach ($vessels as $index => $v) {
            // Distribute A1/A2 so none end up with both.
            if ($index % 2 === 0) { // 0,2,4 -> A1 only (v1, v3, v5)
                $v->equipment()->sync([$a1->id]);
            } else { // 1,3 -> A2 only (v2, v4)
                $v->equipment()->sync([$a2->id]);
            }
        }

        // Create the 6th vessel and attach BOTH A1 and A2
        /** @var Vessel $v6 */
        $v6 = Vessel::factory()->create(['name' => 'Sixth Vessel', 'type' => 'TypeZ', 'size' => 6]);
        $v6->equipment()->sync([$a1->id, $a2->id]);

        // Prepare payload with the specified time window and duplicate requirements
        $payload = [
            'title' => 'Night hop',
            'start_at' => '2025-11-12 02:00:00',
            'end_at' => '2025-11-12 03:00:00',
            'required_equipment' => ['A1', 'A2', 'A2', 'A1'],
        ];

        // First attempt: should reserve on vessel #6 directly (only candidate + available)
        $response1 = $this->post(route('reservations.store'), $payload);
        $response1->assertStatus(200)
            ->assertViewIs('reservations.result')
            ->assertViewHas('success', true)
            ->assertViewHas('vessel');

        $vesselFromView = $response1->viewData('vessel');
        $this->assertInstanceOf(Vessel::class, $vesselFromView);
        $this->assertEquals($v6->id, $vesselFromView->id, 'First reservation must be assigned to vessel #6');

        $this->assertDatabaseHas('reservations', [
            'vessel_id' => $v6->id,
            'title' => 'Night hop',
            'start_at' => Repository::dateFromLocalToDB('2025-11-12 02:00:00', true),
            'end_at' => Repository::dateFromLocalToDB('2025-11-12 03:00:00', true),
        ]);

        // Second attempt: same params; vessel #6 is now busy at that time, so only suggestion should be returned
        $response2 = $this->post(route('reservations.store'), $payload);
        $response2->assertStatus(200)
            ->assertViewIs('reservations.result')
            ->assertViewHas('success', false)
            ->assertViewHas('suggestions');

        $suggestions = $response2->viewData('suggestions');
        $this->assertIsArray($suggestions);
        $this->assertCount(1, $suggestions, 'Exactly one suggestion should be returned');
        $this->assertSame($v6->id, $suggestions[0]['vessel_id'] ?? null, 'Suggested vessel must be #6');
    }
}
