<?php

namespace Database\Seeders;

use App\Models\Vessel;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Illuminate\Support\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        $vessels = Vessel::with('equipment')->get();

        // Tomorrow date
        $tomorrow = Carbon::tomorrow();

        /*
        foreach ($vessels as $vessel) {
            // Create 2 tasks per vessel
            for ($i = 1; $i <= 2; $i++) {
                $start = $tomorrow->copy()->addHours(rand(8, 16)); // start between 8am-4pm
                $end = $start->copy()->addHours(rand(1, 3)); // duration 1-3 hours

                Task::create([
                    'title' => "Task {$i} for {$vessel->name}",
                    'description' => "This is a sample task for {$vessel->name}",
                    'vessel_id' => $vessel->id,
                    'start_at' => $start,
                    'end_at' => $end,
                    'required_equipment' => $vessel->equipment->pluck('code')->toArray(),
                    'status' => 'pending', // optional, adjust as needed
                ]);
            }
        }
        */
    }
}
