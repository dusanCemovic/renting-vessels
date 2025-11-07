<?php

namespace Database\Seeders;

use App\Models\Vessel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $vassels = Vessel::all();
        foreach ($vassels as $vassel) {
            $teamCount = rand(6, 8);
            Task::factory()->count($teamCount)->create([
                'vassel_id' => $vassel->id,
            ]);
        }
        $this->call(VesselSeeder::class);
    }
}
