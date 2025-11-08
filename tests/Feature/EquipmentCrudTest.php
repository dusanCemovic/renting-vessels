<?php

namespace Tests\Feature;

use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_paginated_and_sort_equipment(): void
    {
        Equipment::factory()->create(['code' => 'A1', 'name' => 'Alpha']);
        Equipment::factory()->create(['code' => 'B2', 'name' => 'Bravo']);

        $response = $this->get(route('equipments.index', ['sort' => 'name', 'dir' => 'asc']));
        $response->assertOk();
        $response->assertSeeInOrder(['Alpha', 'Bravo']);

        $response = $this->get(route('equipments.index', ['sort' => 'name', 'dir' => 'desc']));
        $response->assertOk();
        $response->assertSeeInOrder(['Bravo', 'Alpha']);
    }

    public function test_can_create_update_and_delete_equipment(): void
    {
        // create
        $create = $this->post(route('equipments.store'), [
            'code' => 'C9',
            'name' => 'Charlie',
            'description' => 'Desc',
        ]);
        $create->assertRedirect(route('equipments.index'));
        $this->assertDatabaseHas('equipment', ['code' => 'C9', 'name' => 'Charlie']);

        $equipment = Equipment::where('code', 'C9')->firstOrFail();

        // update (code immutable)
        $update = $this->put(route('equipments.update', $equipment), [
            'name' => 'Charlie Updated',
            'description' => 'New desc',
        ]);
        $update->assertRedirect(route('equipments.index'));
        $this->assertDatabaseHas('equipment', ['code' => 'C9', 'name' => 'Charlie Updated']);

        // delete (auto-detach is covered implicitly; no vessels attached here)
        $delete = $this->delete(route('equipments.destroy', $equipment));
        $delete->assertRedirect(route('equipments.index'));
        $this->assertDatabaseMissing('equipment', ['code' => 'C9']);
    }

    public function test_unique_code_is_enforced_on_create(): void
    {
        Equipment::factory()->create(['code' => 'UN1']);
        $resp = $this->from(route('equipments.create'))->post(route('equipments.store'), [
            'code' => 'UN1',
            'name' => 'Duplicate',
        ]);
        $resp->assertSessionHasErrors('code');
    }
}
