<?php

namespace App\Http\Controllers\ALLFIT;

use App\Enums\WorkoutProgramLevelEnum;
use App\Enums\WorkoutProgramTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutProgramRequest;
use App\Models\Exercise;
use App\Models\WorkoutProgram;
use App\Services\WorkoutProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutProgramController extends Controller
{
    public function __construct(private WorkoutProgramService $service) {}

    public function index(): Response
    {
        $programs = WorkoutProgram::query()
            ->where(function ($q) {
                $q->system()->orWhere->forUser(Auth::id());
            })
            ->withCount('programExercises')
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'name'             => $p->name,
                'description'      => $p->description,
                'type'             => $p->type->value,
                'typeLabel'        => $p->type->label(),
                'level'            => $p->level->value,
                'levelLabel'       => $p->level->label(),
                'is_system'        => $p->is_system,
                'exercises_count'  => $p->program_exercises_count,
                'can_edit'         => !$p->is_system && $p->user_id === Auth::id(),
            ]);

        return Inertia::render('WorkoutPrograms/Index', [
            'programs' => $programs,
            'types'    => collect(WorkoutProgramTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
            'levels' => collect(WorkoutProgramLevelEnum::cases())->map(fn($l) => [
                'value' => $l->value,
                'label' => $l->label(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('WorkoutPrograms/Create', [
            'exercises' => $this->availableExercises(),
            'types'     => collect(WorkoutProgramTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
            'levels' => collect(WorkoutProgramLevelEnum::cases())->map(fn($l) => [
                'value' => $l->value,
                'label' => $l->label(),
            ]),
        ]);
    }

    public function store(WorkoutProgramRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('workout-programs.index')
            ->with('success', 'Программа создана.');
    }

    public function edit(WorkoutProgram $workoutProgram): Response
    {
        abort_if(
            !$workoutProgram->is_system && $workoutProgram->user_id !== Auth::id(),
            403
        );

        $programExercises = $workoutProgram->programExercises()
            ->with('exercise')
            ->get()
            ->map(fn($pe) => [
                'exercise_id'   => $pe->exercise_id,
                'exercise_name' => $pe->exercise->name,
                'sets'          => $pe->sets,
                'reps'          => $pe->reps,
                'weight'        => $pe->weight,
                'order'         => $pe->order,
            ]);

        return Inertia::render('WorkoutPrograms/Edit', [
            'program' => [
                'id'          => $workoutProgram->id,
                'name'        => $workoutProgram->name,
                'description' => $workoutProgram->description,
                'type'        => $workoutProgram->type->value,
                'level'       => $workoutProgram->level->value,
                'exercises'   => $programExercises,
            ],
            'exercises' => $this->availableExercises(),
            'types'     => collect(WorkoutProgramTypeEnum::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
            'levels' => collect(WorkoutProgramLevelEnum::cases())->map(fn($l) => [
                'value' => $l->value,
                'label' => $l->label(),
            ]),
        ]);
    }

    public function update(WorkoutProgramRequest $request, WorkoutProgram $workoutProgram): RedirectResponse
    {
        abort_if($workoutProgram->user_id !== Auth::id(), 403);

        $this->service->update($workoutProgram, $request->validated());

        return redirect()->route('workout-programs.index')
            ->with('success', 'Программа обновлена.');
    }

    public function destroy(WorkoutProgram $workoutProgram): RedirectResponse
    {
        abort_if($workoutProgram->user_id !== Auth::id(), 403);

        $workoutProgram->delete();

        return redirect()->route('workout-programs.index')
            ->with('success', 'Программа удалена.');
    }

    private function availableExercises(): \Illuminate\Support\Collection
    {
        return Exercise::query()
            ->where(function ($q) {
                $q->system()->orWhere->forUser(Auth::id());
            })
            ->orderBy('name')
            ->get()
            ->map(fn($e) => [
                'id'            => $e->id,
                'name'          => $e->name,
                'categoryLabel' => $e->category->label(),
                'typeLabel'     => $e->type->label(),
            ]);
    }
}