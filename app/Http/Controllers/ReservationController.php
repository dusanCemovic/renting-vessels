<?php

namespace App\Http\Controllers;

use App\Http\Requests\VesselTaskFilterRequest;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Vessel;
use App\Services\Repository;
use App\Services\VesselReservation;
use Carbon\Carbon;

class ReservationController
{
    public function index(VesselTaskFilterRequest $request) {
        // cleared type, sort and dit
        $filters = $request->validatedFilters();

        // get reservations
        $reservations = Repository::getReservations();

        // sort them based on params
        $reservations = Repository::sort($reservations, $filters['sort'], $filters['dir']);

        return view('reservations.index', [
            'tasks' => $reservations,
            'filters' => [
                'sort' => $filters['sort'],
                'dir' => $filters['dir'],
            ],
        ]);
    }

    public function show(Reservation $reservation)
    {
        return view('reservations.show', compact('reservation'));
    }

    public function create()
    {
        $equipments = Equipment::query()
            ->orderBy('equipment.name', 'asc')->get();
        return view('reservations.create', compact('equipments'));
    }

    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $start = Repository::dateFromLocalToDB($data['start_at']);
        $end = Repository::dateFromLocalToDB($data['end_at']);
        $required = $data['required_equipment'] ?? [];

        // get only vessels with required equipment
        $vessels = VesselReservation::getVesselsWithEquipment($required);

        // get available vessels in that period (working in UTC)
        $available = VesselReservation::checkAvailability($vessels, $start, $end);

        // if we have available vessels, then create task
        if (!empty($available)) {
            $vessel = $available[0];
            $task = Reservation::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'vessel_id' => $vessel->id,
                'start_at' => $start,
                'end_at' => $end,
                'required_equipment' => $required,
            ]);

            return view('reservations.result', [
                'success' => true,
                'task' => $task,
                'vessel' => $vessel
            ]);
        }

        // if not, continue with reservations
        $suggestions = VesselReservation::getSuggestions($vessels, $start, $end);

        return view('reservations.result', [
            'success' => false,
            'suggestions' => $suggestions
        ]);
    }

}
