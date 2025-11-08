<?php

namespace Tests\Unit;

use App\Models\Equipment;
use App\Models\Vessel;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VesselReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_suggestions_when_required_equipment_exceeds_available_on_vessels(): void
    {
        // Arrange: create 4 required equipment items
        $eq1 = Equipment::factory()->create(['code' => 'EQ1']);
        $eq2 = Equipment::factory()->create(['code' => 'EQ2']);
        $eq3 = Equipment::factory()->create(['code' => 'EQ3']);
        $eq4 = Equipment::factory()->create(['code' => 'EQ4']);

        // Create vessels where each has at most 2 equipment items
        $vesselA = Vessel::factory()->create(['name' => 'Alpha']);
        $vesselA->equipment()->attach([$eq1->id, $eq2->id]);

        $vesselB = Vessel::factory()->create(['name' => 'Bravo']);
        $vesselB->equipment()->attach([$eq3->id, $eq4->id]);

        // Required set needs all 4 equipment codes
        $required = ['EQ1', 'EQ2', 'EQ3', 'EQ4'];

        // Act: filter vessels by required equipment (none should qualify)
        $candidates = VesselReservation::getVesselsWithEquipment($required);

        // Guard assertion: ensure no vessel satisfies all 4
        $this->assertCount(0, $candidates, 'No vessel should match all 4 required equipment');

        // Act: ask for suggestions with empty candidate list
        $start = Carbon::now()->addDay();
        $end = $start->copy()->addHours(2);
        $suggestions = VesselReservation::getSuggestions($candidates, $start, $end);

        // Assert: suggestions should be empty
        $this->assertIsArray($suggestions);
        $this->assertCount(0, $suggestions, 'Suggestions must be empty when no vessel meets the required equipment');
    }
}
