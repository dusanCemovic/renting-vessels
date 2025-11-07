<?php

namespace App\Http\Controllers;

use App\Models\Vessel;

class VesselController
{
    public function index()
    {
        // Load all vessels with equipment
        $vessels = Vessel::with('equipment')->get();
        return view('vessels.index', compact('vessels'));
    }

    public function show(Vessel $vessel)
    {
        // Eager load tasks ordered by start time desc
        $vessel->load(['reservations' => function ($q) {
            $q->orderBy('start_at', 'desc');
        }]);

        return view('vessels.show', compact('vessel'));
    }
}
