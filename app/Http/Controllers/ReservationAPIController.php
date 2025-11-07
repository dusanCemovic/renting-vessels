<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vessel;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationAPIController
{
    public function reserve(Request $request)
    {

        // 1. validation
        $data = $request->validate([
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'required_equipment' => 'nullable|array',
        ]);

        // 2. Find vessels that have all required equipment

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);
        $required = $data['required_equipment'] ?? [];


        $vessels = Vessel::with('equipment')
            ->get()
            ->filter(function ($ves) use ($required) {
                $have = $ves->equipment->pluck('code')->toArray();
                // retrieves all the values of code
                return empty(array_diff($required, $have));
            });


        // 3. Filter out vessels unavailable Tasks or Maintenance

        $available = VesselReservation::checkAvailability($vessels, $start, $end);

        if (empty($available)) {
            $vessel = $available->first();
            $task = Reservation::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'vessel_id' => $vessel->id,
                'start_at' => $start,
                'end_at' => $end,
                'required_equipment' => $required,
            ]);

            return response()->json(['success' => true, 'task' => $task, 'vessel' => $vessel]);
        }

        // 4. No vessel free at requested time -> find earliest possible time for any candidate vessel

        $suggestions = VesselReservation::getSuggestions($vessels, $start, $end);


        // 5. Order by earliest suggestion
        usort($suggestions, function ($a, $b) {
            return strcmp($a['available_from'], $b['available_from']);
        });

        return response()->json(['success' => false, 'message' => 'No vessel available at requested time', 'suggestions' => $suggestions]);

    }

    // can be used
    public function vesselTasks($vesselId)
    {
        dd(1111);

        $v = Vessel::with(['tasks' => function ($q) {
            $q->orderBy('start_at', 'desc');
        }])->findOrFail($vesselId);
        return response()->json($v->tasks);
    }

    // additionally thing
    public function addMaintenance(Request $request, $vesselId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'notes' => 'nullable|string'
        ]);

        $vessel = Vessel::findOrFail($vesselId);
        $json_response = $vessel->maintenances()->create($data);

        return response()->json($json_response, 201);
    }


    public function showReserveForm()
    {
        $vessels = Vessel::with('equipment')->get();
        return view('reserve.form', compact('vessels'));
    }

    public function submitReserveForm(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'required_equipment' => 'nullable|array',
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);
        $required = $data['required_equipment'] ?? [];

        $vessels = Vessel::with('equipment')
            ->get()
            ->filter(function ($ves) use ($required) {
                $have = $ves->equipment->pluck('code')->toArray();
                return empty(array_diff($required, $have));
            });

        $available = \App\Models\VesselReservation::checkAvailability($vessels, $start, $end);

        if ($available->isNotEmpty()) {
            $vessel = $available->first();
            $task = Reservation::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'vessel_id' => $vessel->id,
                'start_at' => $start,
                'end_at' => $end,
                'required_equipment' => $required,
            ]);

            return view('reserve.result', [
                'success' => true,
                'task' => $task,
                'vessel' => $vessel
            ]);
        }

        $suggestions = \App\Models\VesselReservation::getSuggestions($vessels, $start, $end);

        usort($suggestions, function ($a, $b) {
            return strcmp($a['available_from'], $b['available_from']);
        });

        return view('reserve.result', [
            'success' => false,
            'suggestions' => $suggestions
        ]);
    }

    public function vesselTasksView($vesselId)
    {
        $vessel = \App\Models\Vessel::with(['tasks' => function ($q) {
            $q->orderBy('start_at', 'desc');
        }])->findOrFail($vesselId);

        return view('vessels.tasks', compact('vessel'));
    }

}
