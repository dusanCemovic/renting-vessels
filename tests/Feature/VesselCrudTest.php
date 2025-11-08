<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VesselCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_update_and_delete_vessel_without_relations(): void
    {
        $e1 = Equipment::factory()->create(['code' => 'A1']);
        $e2 = Equipment::factory()->create(['code' => 'B2']);

        // Create
        $create = $this->post(route('vessels.store'), [
            'name' => 'Falcon',
            'type' => 'jet',
            'size' => 6,
            'equipment' => [$e1->id, $e2->id],
        ]);
        $create->assertRedirect(route('vessels.index'));
        $vessel = Vessel::where('name', 'Falcon')->firstOrFail();
        $this->assertCount(2, $vessel->equipment);

        // Update (sync equipment)
        $update = $this->put(route('vessels.update', $vessel), [
            'name' => 'Falcon X',
            'type' => 'jet',
            'size' => 8,
            'equipment' => [$e1->id],
        ]);
        $update->assertRedirect(route('vessels.index'));
        $vessel->refresh();
        $this->assertEquals('Falcon X', $vessel->name);
        $this->assertEquals(8, $vessel->size);
        $this->assertCount(1, $vessel->equipment);

        // Delete (no relations so allowed, soft delete)
        $delete = $this->delete(route('vessels.destroy', $vessel));
        $delete->assertRedirect(route('vessels.index'));
        $this->assertSoftDeleted('vessels', ['id' => $vessel->id]);
    }

    public function test_unique_name_is_enforced(): void
    {
        $v1 = Vessel::factory()->create(['name' => 'Unique-1']);

        $resp = $this->from(route('vessels.create'))->post(route('vessels.store'), [
            'name' => 'Unique-1',
            'type' => 'prop',
            'size' => 4,
        ]);
        $resp->assertSessionHasErrors('name');

        // Update unique rule ignores current id
        $ok = $this->from(route('vessels.edit', $v1))->put(route('vessels.update', $v1), [
            'name' => 'Unique-1',
            'type' => $v1->type,
            'size' => $v1->size,
        ]);
        $ok->assertRedirect(route('vessels.index'));
    }

    public function test_deletion_is_blocked_when_reservations_exist(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $vessel = Vessel::factory()->create(['name' => 'BusyV']);
        Reservation::create([
            'title' => 'R1',
            'vessel_id' => $vessel->id,
            'start_at' => Carbon::parse('2025-01-02 10:00:00'),
            'end_at' => Carbon::parse('2025-01-02 12:00:00'),
            'required_equipment' => [],
        ]);

        $resp = $this->delete(route('vessels.destroy', $vessel));
        $resp->assertRedirect(route('vessels.index'));
        $resp->assertSessionHas('error');
        $this->assertDatabaseHas('vessels', ['id' => $vessel->id, 'deleted_at' => null]);
    }
}
