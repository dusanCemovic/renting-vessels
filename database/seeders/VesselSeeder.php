<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vessel;
use App\Models\Equipment;

class VesselSeeder extends Seeder
{
    public function run()
    {
        // Ensure equipment exists (seeded by EquipmentSeeder); fetch by code
        $equipmentByCode = Equipment::query()->pluck('id', 'code');

        $v1 = Vessel::create(['name' => 'Jet 1', 'type' => 'jet', 'size' => 6]);
        $v2 = Vessel::create(['name' => 'Jet 2', 'type' => 'prop', 'size' => 4]);
        $v3 = Vessel::create(['name' => 'Jet 3', 'type' => 'prop', 'size' => 6]);
        $v4 = Vessel::create(['name' => 'Jet 4', 'type' => 'jet', 'size' => 4]);
        $v5 = Vessel::create(['name' => 'Jet 5', 'type' => 'prop', 'size' => 4]);

        // Attach equipment using codes defined in EquipmentSeeder
        $map = fn(array $codes) => array_values(array_filter(array_map(fn($c) => $equipmentByCode[$c] ?? null, $codes)));
        $v1->equipment()->attach($map(['A1','A2']));
        $v2->equipment()->attach($map(['A2']));
        $v3->equipment()->attach($map(['A1','B1']));
        $v4->equipment()->attach($map(['A2','B2']));
        // v5 no equipment
    }
}
