<?php

namespace App\Http\Requests\Vessel;

use Illuminate\Foundation\Http\FormRequest;

class StoreVesselRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:vessels,name'],
            'type' => ['required', 'string', 'max:50'],
            'size' => ['required', 'integer', 'min:1'],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['integer', 'exists:equipment,id'],
        ];
    }
}
