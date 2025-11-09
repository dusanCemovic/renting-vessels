<?php

namespace App\Http\Controllers;

use App\Http\Requests\VesselTaskFilterRequest;
use App\Models\Maintenance;
use App\Models\Vessel;
use App\Services\Repository;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaintenanceController
{
    public function index(VesselTaskFilterRequest $request)
    {
        // cleared type, sort and dit
        $filters = $request->validatedFilters();

        // get maintenances
        $maintenances = Repository::getMaintenances();

        // sort them based on params
        $maintenances = Repository::sort($maintenances, $filters['sort'], $filters['dir']);

        return view('maintenances.index', [
            'tasks' => $maintenances,
            'filters' => [
                'sort' => $filters['sort'],
                'dir' => $filters['dir'],
            ],
        ]);
    }

    public function show(Maintenance $maintenance)
    {
        return view('maintenances.show', compact('maintenance'));
    }

    public function create(Vessel $vessel)
    {
        return view('maintenances.create', [
            'vessel' => $vessel,
        ]);
    }

    public function store(Request $request, Vessel $vessel)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ]);

        $start = Repository::dateFromLocalToDB($data['start_at']);
        $end = Repository::dateFromLocalToDB($data['end_at']);

        // get available vessels in that period (working in UTC)
        $available = VesselReservation::checkAvailability([$vessel], $start, $end);

        // if we have available vessels, then create task
        if (!empty($available)) {
            $vessel->maintenances()->create(
                [
                    'title' => $data['title'],
                    'notes' => $data['notes'] ?? '',
                    'start_at' => $start,
                    'end_at' => $end,
                ]
            );

            return redirect()->route('vessels.show', $vessel)
                ->with('status', 'Maintenance created successfully.');
        } else {

            $suggestions = VesselReservation::getSuggestions([$vessel], $start, $end);

            throw ValidationException::withMessages([
                'info' => 'Vessel is busy in that period.',
                'suggestion'    => isset($suggestions[0]) ?
                    "First next available slot is in: " . Carbon::parse($suggestions[0]['available_from'])
                    ->setTimezone('Europe/Ljubljana')
                    ->format('j F Y, H:i')
                    : null,
            ]);
        }
    }
}
