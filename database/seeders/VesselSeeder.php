<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vessel;
use App\Models\Equipment;

class VesselSeeder extends Seeder
{
    public function run()
    {
        // Create sample equipment
        $eq1 = Equipment::firstOrCreate(['code' => 'A1'], ['name' => 'AddOn A1']);
        $eq2 = Equipment::firstOrCreate(['code' => 'A2'], ['name' => 'AddOn A2']);
        $eq3 = Equipment::firstOrCreate(['code' => 'B1'], ['name' => 'AddOn B1']);
        $eq4 = Equipment::firstOrCreate(['code' => 'B2'], ['name' => 'AddOn B2']);

        $v1 = Vessel::create(['name' => 'Jet 1', 'type' => 'jet', 'size' => 6]);
        $v2 = Vessel::create(['name' => 'Jet 2', 'type' => 'prop', 'size' => 4]);
        $v3 = Vessel::create(['name' => 'Jet 3', 'type' => 'prop', 'size' => 6]);
        $v4 = Vessel::create(['name' => 'Jet 4', 'type' => 'jet', 'size' => 4]);
        $v5 = Vessel::create(['name' => 'Jet 5', 'type' => 'prop', 'size' => 4]);

        $v1->equipment()->attach([$eq1->id,$eq2->id]);
        $v2->equipment()->attach([$eq2->id]);
        $v3->equipment()->attach([$eq1->id,$eq3->id]);
        $v4->equipment()->attach([$eq2->id,$eq4->id]);
        $v5->equipment()->attach([]);
    }
}
