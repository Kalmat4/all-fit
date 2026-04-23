<?php

namespace App\Http\Controllers;

use App\Enums\ExerciseCategoryEnum;
use App\Enums\ExerciseTypeEnum;
use App\Http\Requests\ExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function index(): Response
    {
        $exercises = Exercise::query()
            ->where(function ($q) {
                $q->system()->orWhere->forUser(Auth::id());
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn($e) => [
                'id'          => $e->id,
                'name'        => $e->name,
                'category'    => $e->category->value,
                'categoryLabel' => $e->category->label(),
                'type'        => $e->type->value,
                'typeLabel'   => $e->type->label(),
                'description' => $e->description,
                'is_system'   => $e->is_system,
                'can_edit'    => !$e->is_system && $e->user_id === Auth::id(),
            ]);

        return Inertia::render('Exercises/Index', [
            'exercises'  => $exercises,
            'categories' => collect(ExerciseCategoryEnum::cases())->map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ]),
            'types' => collect(ExerciseTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Exercises/Create', [
            'categories' => collect(ExerciseCategoryEnum::cases())->map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ]),
            'types' => collect(ExerciseTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function store(ExerciseRequest $request): RedirectResponse
    {
        Exercise::create([
            ...$request->validated(),
            'user_id'   => Auth::id(),
            'is_system' => false,
        ]);

        return redirect()->route('exercises.index')
            ->with('success', 'Упражнение создано.');
    }

    public function edit(Exercise $exercise): Response
    {
        abort_if(
            !$exercise->is_system && $exercise->user_id !== Auth::id(),
            403
        );

        return Inertia::render('Exercises/Edit', [
            'exercise' => [
                'id'          => $exercise->id,
                'name'        => $exercise->name,
                'category'    => $exercise->category->value,
                'type'        => $exercise->type->value,
                'description' => $exercise->description,
            ],
            'categories' => collect(ExerciseCategoryEnum::cases())->map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ]),
            'types' => collect(ExerciseTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function update(ExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        abort_if($exercise->user_id !== Auth::id(), 403);

        $exercise->update($request->validated());

        return redirect()->route('exercises.index')
            ->with('success', 'Упражнение обновлено.');
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        abort_if($exercise->user_id !== Auth::id(), 403);

        $exercise->delete();

        return redirect()->route('exercises.index')
            ->with('success', 'Упражнение удалено.');
    }
}