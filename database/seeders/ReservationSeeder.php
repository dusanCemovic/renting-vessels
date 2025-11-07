<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // DO NOT DELETE THOSE NEXT COMMENTS
        // Everything at the 10. day after now.
        // first plane reserved from 8h to 10h,  16h to 18h (once only A1, once A1, A2)
        // second plane reserved from 9h to 11h,  12h to 14h (once A2, one nothing)
        // third plane reserved from 7h to 10h,  20h to 22h (once A1, once B1)
        // fourth plane reserved from 11h to 12h,  12h to 13:30h (once both, once only B2)
        // fifth plane reserved from 8h to 10h,  16h to 18h (both nothing)

        // Idempotent deterministic seeding for the exact 10th day from now
        $targetDay = Carbon::now()->addDays(10)->startOfDay();
        $dayStart = $targetDay->copy();
        $dayEnd = $targetDay->copy()->endOfDay();

        // Clear existing reservations for that day to keep it idempotent
        Reservation::whereBetween('start_at', [$dayStart, $dayEnd])->delete();

        // Fetch first five vessels in stable order
        $vessels = Vessel::orderBy('id')->take(5)->get();
        if ($vessels->isEmpty()) {
            return; // nothing to seed
        }

        // Helper to compute start/end Carbon for a given HH:MM:SS string on the target day
        $at = fn(string $time) => $targetDay->copy()->setTimeFromTimeString($time);

        // Map schedules per the specification using the factory
        // 1) first plane: 08:00-10:00 (A1), 16:00-18:00 (A1,A2)
        if (isset($vessels[0])) {
            $v = $vessels[0];
            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V1 Slot 1',
                'start_at' => $at('08:00:00'),
                'end_at' => $at('10:00:00'),
                'required_equipment' => ['A1'],
            ])->create();

            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V1 Slot 2',
                'start_at' => $at('16:00:00'),
                'end_at' => $at('18:00:00'),
                'required_equipment' => ['A1', 'A2'],
            ])->create();
        }

        // 2) second plane: 09:00-11:00 (A2), 12:00-14:00 ([])
        if (isset($vessels[1])) {
            $v = $vessels[1];
            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V2 Slot 1',
                'start_at' => $at('09:00:00'),
                'end_at' => $at('11:00:00'),
                'required_equipment' => ['A2'],
            ])->create();

            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V2 Slot 2',
                'start_at' => $at('12:00:00'),
                'end_at' => $at('14:00:00'),
                'required_equipment' => [],
            ])->create();
        }

        // 3) third plane: 07:00-10:00 (A1), 20:00-22:00 (B1)
        if (isset($vessels[2])) {
            $v = $vessels[2];
            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V3 Slot 1',
                'start_at' => $at('07:00:00'),
                'end_at' => $at('10:00:00'),
                'required_equipment' => ['A1'],
            ])->create();

            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V3 Slot 2',
                'start_at' => $at('20:00:00'),
                'end_at' => $at('22:00:00'),
                'required_equipment' => ['B1'],
            ])->create();
        }

        // 4) fourth plane: 11:00-12:00 (A2 & B2), 12:00-13:30 (B2)
        if (isset($vessels[3])) {
            $v = $vessels[3];
            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V4 Slot 1',
                'start_at' => $at('11:00:00'),
                'end_at' => $at('12:00:00'),
                'required_equipment' => ['A2', 'B2'],
            ])->create();

            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V4 Slot 2',
                'start_at' => $at('12:00:00'),
                'end_at' => $at('13:30:00'),
                'required_equipment' => ['B2'],
            ])->create();
        }

        // 5) fifth plane: 08:00-10:00 ([]), 16:00-18:00 ([])
        if (isset($vessels[4])) {
            $v = $vessels[4];
            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V5 Slot 1',
                'start_at' => $at('08:00:00'),
                'end_at' => $at('10:00:00'),
                'required_equipment' => [],
            ])->create();

            Reservation::factory()->for($v, 'vessel')->state([
                'title' => 'Rsv V5 Slot 2',
                'start_at' => $at('16:00:00'),
                'end_at' => $at('18:00:00'),
                'required_equipment' => [],
            ])->create();
        }
    }
}
