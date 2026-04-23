<?php

namespace App\Http\Requests;

use App\Enums\ExerciseCategoryEnum;
use App\Enums\ExerciseTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', new Enum(ExerciseCategoryEnum::class)],
            'type'        => ['required', new Enum(ExerciseTypeEnum::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}