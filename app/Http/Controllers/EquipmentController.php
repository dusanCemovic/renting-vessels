<?php

namespace App\Http\Controllers;

use App\Http\Requests\Equipment\ListEquipmentFilterRequest;
use App\Http\Requests\Equipment\StoreEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EquipmentController
{
    public function index(ListEquipmentFilterRequest $request): View
    {
        extract($request->validatedFilters()); // sort and dir

        $equipments = Equipment::query()
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->appends(['sort' => $sort, 'dir' => $dir]);

        return view('equipments.index', compact('equipments', 'sort', 'dir'));
    }

    public function create(): View
    {
        return view('equipments.create');
    }

    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        $equipment = Equipment::create($request->validated());
        return redirect()->route('equipments.index')
            ->with('status', "Equipment {$equipment->code} created.");
    }

    public function edit(Equipment $equipment): View
    {
        return view('equipments.edit', compact('equipment'));
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        // code is immutable; ensure we don't allow changes even if sent
        $data = $request->validated();
        unset($data['code']);
        $equipment->update($data);
        return redirect()->route('equipments.index')
            ->with('status', "Equipment {$equipment->code} updated.");
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        // detach from all vessels then delete
        $equipment->vessels()->detach();
        $equipment->delete();
        return redirect()->route('equipments.index')
            ->with('status', "Equipment {$equipment->code} deleted.");
    }
}
