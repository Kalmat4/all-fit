<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkoutSessionSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'set_number' => ['required', 'integer', 'min:1'],
            'reps'       => ['required', 'string', 'max:50'],
            'weight'     => ['nullable', 'numeric', 'min:0'],
        ];
    }
}