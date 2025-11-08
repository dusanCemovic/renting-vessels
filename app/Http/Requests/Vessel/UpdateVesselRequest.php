<?php

namespace App\Http\Requests\Vessel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVesselRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('vessel')?->id ?? null;
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('vessels', 'name')->ignore($id)],
            'type' => ['required', 'string', 'max:50'],
            'size' => ['required', 'integer', 'min:1'],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['integer', 'exists:equipment,id'],
        ];
    }
}
