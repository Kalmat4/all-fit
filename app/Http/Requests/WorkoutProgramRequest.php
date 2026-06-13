<?php

namespace App\Http\Requests;

use App\Enums\ExerciseModeEnum;
use App\Enums\WorkoutProgramLevelEnum;
use App\Enums\WorkoutProgramTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
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
            'exercises.*.mode'           => ['required', new Enum(ExerciseModeEnum::class)],
            'exercises.*.sets'           => ['required', 'integer', 'min:1', 'max:20'],
            'exercises.*.reps'           => ['required', 'string'],
            'exercises.*.weight'         => ['nullable', 'numeric', 'min:0'],
            'exercises.*.target_reps'    => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->sometimes(
            'exercises.*.target_reps',
            ['required', 'integer', 'min:1'],
            fn($input) => $input->mode === ExerciseModeEnum::TargetReps->value
        );
    }
}