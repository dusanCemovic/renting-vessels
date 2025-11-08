<?php

namespace App\Http\Requests\Vessel;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Filters for listing vessels (sorting + direction).
 */
class ListVesselFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules() : array
    {
        return [
            'sort' => 'in:name,type,size',
            'dir'  => 'in:asc,desc',
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalize input before validation
        $this->merge([
            'sort' => strtolower($this->query('sort', 'name')),
            'dir'  => strtolower($this->query('dir', 'asc')),
        ]);
    }

    public function validatedFilters(): array
    {
        // Return normalized & validated values
        return [
            'sort' => $this->input('sort', 'name'),
            'dir'  => $this->input('dir', 'asc'),
        ];
    }
}
