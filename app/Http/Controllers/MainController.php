<?php

namespace App\Http\Controllers;

use App\Http\Requests\VesselTaskFilterRequest;
use App\Services\Repository;

class MainController
{
    public function index(VesselTaskFilterRequest $request) {

        // cleared type, sort and dit
        $filters = $request->validatedFilters();

        // get based on filter
        $tasks = collect();
        if ($filters['type'] === 'both' || $filters['type'] === 'reservations') {
            $tasks = $tasks->concat(Repository::getReservations());
        }
        if ($filters['type'] === 'both' || $filters['type'] === 'maintenance') {
            $tasks = $tasks->concat(Repository::getMaintenances());
        }

        // sort them based on params
        $tasks = Repository::sort($tasks, $filters['sort'], $filters['dir']);

        return view('home', [
            'tasks' => $tasks,
            'filters' => [
                'type' => $filters['type'],
                'sort' => $filters['sort'],
                'dir' => $filters['dir'],
            ],
            'showTypeFilter' => true,
        ]);
    }
}
