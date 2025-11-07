<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vessel;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController
{

    public function index() {
        $tasks = Reservation::with('vessel')->orderByDesc('start_at')->get();
        return view('reservations.index', compact('tasks'));
    }

    public function show(Reservation $reservation)
    {
        return view('reservations.show', compact('reservation'));
    }

    public function create()
    {
        $vessels = Vessel::with('equipment')->get();
        return view('reserve.form', compact('vessels'));
    }

    public function store(Request $request)
    {
        // Validate input. This can be new classes extended from Request
        $data = $request->validate([
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'required_equipment' => 'nullable|array',
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);
        $required = $data['required_equipment'] ?? [];

        // get only vessels with required equipment
        $vessels = VesselReservation::getVesselsWithEquipment($required);

        // get available vessels in that period
        $available = VesselReservation::checkAvailability($vessels, $start, $end);

        // if we have, then create task
        if (empty(!$available)) {
            $vessel = $available[0];
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

        // if not, continue with reservations
        $suggestions = VesselReservation::getSuggestions($vessels, $start, $end);

        return view('reserve.result', [
            'success' => false,
            'suggestions' => $suggestions
        ]);
    }

    public function vesselTasksView($vesselId)
    {
        $vessel = Vessel::with(['tasks' => function ($q) {
            $q->orderBy('start_at', 'desc');
        }])->findOrFail($vesselId);

        return view('tasks', compact('vessel'));
    }

    public function viewAllVessels()
    {
        // Load all vessels with equipment
        $vessels = \App\Models\Vessel::with('equipment')->get();

        return view('vessels.index', compact('vessels'));
    }

}
