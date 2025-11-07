<?php

namespace App\Http\Controllers;

use App\Http\Requests\VesselFilterRequest;
use App\Models\Reservation;
use App\Models\Maintenance;
use App\Models\Vessel;
use App\Services\Repository;
use App\Services\VesselReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController
{
    public function index(VesselFilterRequest $request) {
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

//    public function create()
//    {
//        $vessels = Vessel::with('equipment')->get();
//        return view('reserve.form', compact('vessels'));
//    }

}
