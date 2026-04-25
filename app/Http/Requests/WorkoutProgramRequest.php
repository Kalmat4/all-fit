<?php

namespace App\Http\Requests;

use App\Enums\WorkoutProgramLevelEnum;
use App\Enums\WorkoutProgramTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class WorkoutProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => ['required', 'string', 'max:255'],
            'description'                => ['nullable', 'string', 'max:1000'],
            'type'                       => ['required', new Enum(WorkoutProgramTypeEnum::class)],
            'level'                      => ['required', new Enum(WorkoutProgramLevelEnum::class)],
            'exercises'                  => ['array'],
            'exercises.*.exercise_id'    => ['required', 'exists:exercises,id'],
            'exercises.*.sets'           => ['required', 'integer', 'min:1', 'max:20'],
            'exercises.*.reps'           => ['required', 'string'],
            'exercises.*.weight'         => ['nullable', 'numeric', 'min:0'],
        ];
    }
}