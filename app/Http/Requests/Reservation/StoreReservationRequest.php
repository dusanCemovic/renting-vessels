<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'required_equipment' => ['nullable', 'array'],
            'required_equipment.*' => ['string'],
        ];
    }

    public function prepareForValidation(): void
    {
        $title = $this->input('title');
        $description = $this->input('description');
        $required = $this->input('required_equipment');

        if (is_string($title)) {
            $title = trim($title);
        }
        if (is_string($description)) {
            $description = trim($description);
        }

        // this part is probably not necessary, but is a good practice

        if (is_string($required)) {
            // allow comma-separated string from unexpected clients -> convert to array
            $required = array_filter(array_map('trim', explode(',', $required)));
        }

        if (is_array($required)) {
            // normalize values to codes as strings, trim spaces
            $required = array_values(array_filter(array_map(function ($v) {
                return is_string($v) ? trim($v) : $v;
            }, $required), function ($v) {
                return $v !== '' && $v !== null;
            }));
        }

        $this->merge([
            'title' => $title,
            'description' => $description,
            'required_equipment' => $required,
        ]);
    }
}
