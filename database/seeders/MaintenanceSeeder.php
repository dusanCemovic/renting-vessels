<?php

namespace Database\Seeders;

use App\Models\Maintenance;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        // Everything at the 10. day after now.
        // first plane maintenance from 12h to 13h
        // second plane maintenance from 17h to 18h
        // third plane without maintenance
        // fourth plane maintenance from 7h to 10h
        // fifth plane maintenance from 14h to 16h

        // Idempotent deterministic seeding for the exact 10th day from now
        $targetDay = Carbon::now()->addDays(10)->startOfDay();
        $dayStart = $targetDay->copy();
        $dayEnd = $targetDay->copy()->endOfDay();

        // Clear existing maintenances for that day to keep it idempotent
        Maintenance::whereBetween('start_at', [$dayStart, $dayEnd])->delete();

        // Fetch first five vessels in stable order
        $vessels = Vessel::orderBy('id')->take(5)->get();
        if ($vessels->isEmpty()) {
            return; // nothing to seed
        }

        // Helper to compute start/end Carbon for a given HH:MM:SS string on the target day
        $at = fn(string $time) => $targetDay->copy()->setTimeFromTimeString($time);

        // 1) first plane: 12:00-13:00
        if (isset($vessels[0])) {
            Maintenance::create([
                'title' => 'Maint V1 Slot 1',
                'vessel_id' => $vessels[0]->id,
                'start_at' => $at('12:00:00'),
                'end_at' => $at('13:00:00'),
            ]);
        }

        // 2) second plane: 17:00-18:00
        if (isset($vessels[1])) {
            Maintenance::create([
                'title' => 'Maint V2 Slot 1',
                'vessel_id' => $vessels[1]->id,
                'start_at' => $at('17:00:00'),
                'end_at' => $at('18:00:00'),
            ]);
        }

        // 3) third plane: no maintenance
        // (intentionally left empty)

        // 4) fourth plane: 07:00-10:00
        if (isset($vessels[3])) {
            Maintenance::create([
                'title' => 'Maint V4 Slot 1',
                'vessel_id' => $vessels[3]->id,
                'start_at' => $at('07:00:00'),
                'end_at' => $at('10:00:00'),
            ]);
        }

        // 5) fifth plane: 14:00-16:00
        if (isset($vessels[4])) {
            Maintenance::create([
                'title' => 'Maint V5 Slot 1',
                'vessel_id' => $vessels[4]->id,
                'start_at' => $at('14:00:00'),
                'end_at' => $at('16:00:00'),
            ]);
        }
    }
}
