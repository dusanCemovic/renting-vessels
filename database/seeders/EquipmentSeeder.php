<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Hardcoded equipment list (idempotent)
        $items = [
            ['code' => 'A1', 'name' => 'AddOn A1'],
            ['code' => 'A2', 'name' => 'AddOn A2'],
            ['code' => 'B1', 'name' => 'AddOn B1'],
            ['code' => 'B2', 'name' => 'AddOn B2'],
        ];

        foreach ($items as $it) {
            Equipment::factory(['code' => $it['code']], ['name' => $it['name']]);
        }
    }
}
