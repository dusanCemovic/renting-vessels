<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vessel\StoreVesselRequest;
use App\Http\Requests\Vessel\UpdateVesselRequest;
use App\Http\Requests\Vessel\ListVesselFilterRequest;
use App\Models\Equipment;
use App\Models\Vessel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VesselController
{
    public function index(ListVesselFilterRequest $request): View
    {
        extract($request->validatedFilters()); // sort and dir

        $vessels = Vessel::with('equipment')
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->appends(['sort' => $sort, 'dir' => $dir]);

        return view('vessels.index', compact('vessels', 'sort', 'dir'));
    }

    public function show(Vessel $vessel): View
    {
        // Eager load tasks ordered by start time desc
        $vessel->load(['reservations' => function ($q) {
            $q->orderBy('start_at', 'desc');
        }, 'equipment']);

        return view('vessels.show', compact('vessel'));
    }

    public function create(): View
    {
        $allEquipment = Equipment::orderBy('code')->get();
        return view('vessels.create', compact('allEquipment'));
    }

    public function store(StoreVesselRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // get equipment and moved them from $data array, so we can send data to  Vessel create
        $equipmentIds = $data['equipment'] ?? [];
        unset($data['equipment']);

        $vessel = Vessel::create($data);
        $vessel->equipment()->sync($equipmentIds);

        return redirect()->route('vessels.index')
            ->with('status', "Vessel {$vessel->name} created.");
    }

    public function edit(Vessel $vessel): View
    {
        $allEquipment = Equipment::orderBy('code')->get();
        $vessel->load('equipment');
        return view('vessels.edit', compact('vessel', 'allEquipment'));
    }

    public function update(UpdateVesselRequest $request, Vessel $vessel): RedirectResponse
    {
        $data = $request->validated();
        $equipmentIds = $data['equipment'] ?? [];
        unset($data['equipment']);

        $vessel->update($data);
        $vessel->equipment()->sync($equipmentIds);

        return redirect()->route('vessels.index')
            ->with('status', "Vessel {$vessel->name} updated.");
    }

    public function destroy(Vessel $vessel): RedirectResponse
    {
        // Block deletion if vessel has reservations or maintenances to preserve history integrity
        $hasRelations = $vessel->reservations()->exists() || $vessel->maintenances()->exists();
        if ($hasRelations) {
            return redirect()->route('vessels.index')
                ->with('error', "Cannot delete vessel {$vessel->name} because it has reservations or maintenances.");
        }

        $vessel->delete(); // soft delete when safe
        return redirect()->route('vessels.index')
            ->with('status', "Vessel {$vessel->name} deleted.");
    }
}
