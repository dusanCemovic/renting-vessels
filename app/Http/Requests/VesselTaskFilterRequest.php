<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * This request is used for listing reservations and maintenances on home or on separate controllers for those two things
 */
class VesselTaskFilterRequest extends FormRequest
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
            'type' => 'in:reservations,maintenance,both',
            'sort' => 'in:vessel,start,end,type',
            'dir'  => 'in:asc,desc',
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalize input before validation
        $this->merge([
            'type' => strtolower($this->query('type', 'both')),
            'sort' => strtolower($this->query('sort', 'start')),
            'dir'  => strtolower($this->query('dir', 'asc')),
        ]);
    }

    public function validatedFilters()
    {
        // Return normalized & validated values
        return [
            'type' => $this->input('type', 'both'),
            'sort' => $this->input('sort', 'start'),
            'dir'  => $this->input('dir', 'asc'),
        ];
    }
}
