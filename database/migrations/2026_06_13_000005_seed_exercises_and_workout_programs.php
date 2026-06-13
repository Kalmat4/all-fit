<?php

use App\Enums\ExerciseCategoryEnum;
use App\Enums\ExerciseModeEnum;
use App\Enums\ExerciseTypeEnum;
use App\Enums\WorkoutProgramLevelEnum;
use App\Enums\WorkoutProgramTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $exerciseNames = [
        // Зал
        'Жим штанги лежа',
        'Жим гантелей на наклонной скамье',
        'Тяга штанги в наклоне',
        'Тяга верхнего блока к груди',
        'Приседания со штангой',
        'Жим ногами в тренажёре',
        'Жим штанги стоя',
        'Махи гантелями в стороны',
        'Подъём штанги на бицепс',
        'Французский жим лежа',
        // Стрит-воркаут
        'Подтягивания широким хватом',
        'Подтягивания узким обратным хватом',
        'Подтягивания нейтральным хватом',
        'Негативные подтягивания',
        'Выход силой на турнике',
        'Подъём ног в висе на турнике',
        'Отжимания на брусьях',
        'Отжимания на брусьях узким хватом',
        'Уголок на брусьях',
        'Подъём коленей к груди на брусьях',
    ];

    private array $programNames = [
        'Верх тела: грудь, спина, плечи',
        'Низ тела и руки',
        'Фулбоди для начинающих',
        'Стрит-воркаут: турник для начинающих',
        'Стрит-воркаут: брусья (продвинутый)',
    ];

    public function up(): void
    {
        $now = now();

        $exercises = [
            // ===== Зал =====
            ['name' => 'Жим штанги лежа', 'category' => ExerciseCategoryEnum::Chest->value, 'description' => 'Базовое упражнение для груди — жим штанги лежа на горизонтальной скамье.'],
            ['name' => 'Жим гантелей на наклонной скамье', 'category' => ExerciseCategoryEnum::Chest->value, 'description' => 'Жим гантелей на скамье с наклоном — акцент на верхнюю часть груди.'],
            ['name' => 'Тяга штанги в наклоне', 'category' => ExerciseCategoryEnum::Back->value, 'description' => 'Базовое упражнение для спины — тяга штанги к поясу в наклоне.'],
            ['name' => 'Тяга верхнего блока к груди', 'category' => ExerciseCategoryEnum::Back->value, 'description' => 'Упражнение на широчайшие мышцы спины в блочном тренажёре.'],
            ['name' => 'Приседания со штангой', 'category' => ExerciseCategoryEnum::Legs->value, 'description' => 'Базовое упражнение для ног — приседания со штангой на плечах.'],
            ['name' => 'Жим ногами в тренажёре', 'category' => ExerciseCategoryEnum::Legs->value, 'description' => 'Упражнение для квадрицепсов и ягодиц в тренажёре для жима ногами.'],
            ['name' => 'Жим штанги стоя', 'category' => ExerciseCategoryEnum::Shoulders->value, 'description' => 'Базовое упражнение для плеч — жим штанги над головой стоя.'],
            ['name' => 'Махи гантелями в стороны', 'category' => ExerciseCategoryEnum::Shoulders->value, 'description' => 'Изолирующее упражнение на средние пучки дельтовидных мышц.'],
            ['name' => 'Подъём штанги на бицепс', 'category' => ExerciseCategoryEnum::Arms->value, 'description' => 'Изолирующее упражнение на бицепс — подъём штанги стоя.'],
            ['name' => 'Французский жим лежа', 'category' => ExerciseCategoryEnum::Arms->value, 'description' => 'Изолирующее упражнение на трицепс — жим штанги лежа узким хватом за голову.'],

            // ===== Стрит-воркаут (турник + брусья) =====
            ['name' => 'Подтягивания широким хватом', 'category' => ExerciseCategoryEnum::Back->value, 'description' => 'Подтягивания на турнике широким хватом — развивают широчайшие мышцы спины. Оборудование: турник.'],
            ['name' => 'Подтягивания узким обратным хватом', 'category' => ExerciseCategoryEnum::Arms->value, 'description' => 'Подтягивания на турнике узким обратным хватом — акцент на бицепс. Оборудование: турник.'],
            ['name' => 'Подтягивания нейтральным хватом', 'category' => ExerciseCategoryEnum::Back->value, 'description' => 'Подтягивания на турнике нейтральным хватом (ладони друг к другу). Оборудование: турник.'],
            ['name' => 'Негативные подтягивания', 'category' => ExerciseCategoryEnum::Back->value, 'description' => 'Медленное контролируемое опускание из верхней точки подтягивания. Оборудование: турник.'],
            ['name' => 'Выход силой на турнике', 'category' => ExerciseCategoryEnum::FullBody->value, 'description' => 'Силовой выход из виса в упор на турнике — комплексное упражнение на спину, плечи и трицепс. Оборудование: турник.'],
            ['name' => 'Подъём ног в висе на турнике', 'category' => ExerciseCategoryEnum::Core->value, 'description' => 'Подъём прямых ног в висе на турнике — упражнение на мышцы кора. Оборудование: турник.'],
            ['name' => 'Отжимания на брусьях', 'category' => ExerciseCategoryEnum::Chest->value, 'description' => 'Отжимания на брусьях с наклоном корпуса вперёд — акцент на грудные мышцы. Оборудование: брусья.'],
            ['name' => 'Отжимания на брусьях узким хватом', 'category' => ExerciseCategoryEnum::Arms->value, 'description' => 'Отжимания на брусьях с вертикальным корпусом — акцент на трицепс. Оборудование: брусья.'],
            ['name' => 'Уголок на брусьях', 'category' => ExerciseCategoryEnum::Core->value, 'description' => 'Статическое удержание ног параллельно полу в упоре на брусьях. Оборудование: брусья.'],
            ['name' => 'Подъём коленей к груди на брусьях', 'category' => ExerciseCategoryEnum::Core->value, 'description' => 'Подъём коленей к груди в упоре на брусьях — упражнение на мышцы кора. Оборудование: брусья.'],
        ];

        $gymCount = 10;
        foreach ($exercises as $index => &$exercise) {
            $exercise['user_id']    = null;
            $exercise['type']       = $index < $gymCount ? ExerciseTypeEnum::Weighted->value : ExerciseTypeEnum::Calisthenics->value;
            $exercise['is_system']  = true;
            $exercise['created_at'] = $now;
            $exercise['updated_at'] = $now;
        }
        unset($exercise);

        DB::table('exercises')->insert($exercises);

        $exerciseIds = DB::table('exercises')
            ->where('is_system', true)
            ->whereIn('name', $this->exerciseNames)
            ->pluck('id', 'name');

        $programs = [
            [
                'name'        => 'Верх тела: грудь, спина, плечи',
                'description' => 'Силовая тренировка на грудь, спину и плечи в зале.',
                'type'        => WorkoutProgramTypeEnum::Weighted->value,
                'level'       => WorkoutProgramLevelEnum::Intermediate->value,
                'exercises'   => [
                    ['name' => 'Жим штанги лежа', 'sets' => 4, 'reps' => '8', 'weight' => 40],
                    ['name' => 'Тяга штанги в наклоне', 'sets' => 4, 'reps' => '10', 'weight' => 40],
                    ['name' => 'Жим штанги стоя', 'sets' => 3, 'reps' => '10', 'weight' => 30],
                    ['name' => 'Тяга верхнего блока к груди', 'sets' => 3, 'reps' => '12', 'weight' => 35],
                    ['name' => 'Жим гантелей на наклонной скамье', 'sets' => 3, 'reps' => '10', 'weight' => 14],
                    ['name' => 'Махи гантелями в стороны', 'sets' => 3, 'reps' => '15', 'weight' => 6],
                ],
            ],
            [
                'name'        => 'Низ тела и руки',
                'description' => 'Силовая тренировка на ноги, бицепс и трицепс в зале.',
                'type'        => WorkoutProgramTypeEnum::Weighted->value,
                'level'       => WorkoutProgramLevelEnum::Intermediate->value,
                'exercises'   => [
                    ['name' => 'Приседания со штангой', 'sets' => 4, 'reps' => '8', 'weight' => 50],
                    ['name' => 'Жим ногами в тренажёре', 'sets' => 3, 'reps' => '12', 'weight' => 80],
                    ['name' => 'Подъём штанги на бицепс', 'sets' => 3, 'reps' => '12', 'weight' => 20],
                    ['name' => 'Французский жим лежа', 'sets' => 3, 'reps' => '12', 'weight' => 20],
                ],
            ],
            [
                'name'        => 'Фулбоди для начинающих',
                'description' => 'Лёгкая тренировка на всё тело для новичков в зале.',
                'type'        => WorkoutProgramTypeEnum::Weighted->value,
                'level'       => WorkoutProgramLevelEnum::Beginner->value,
                'exercises'   => [
                    ['name' => 'Жим штанги лежа', 'sets' => 3, 'reps' => '10', 'weight' => 20],
                    ['name' => 'Тяга штанги в наклоне', 'sets' => 3, 'reps' => '10', 'weight' => 20],
                    ['name' => 'Приседания со штангой', 'mode' => ExerciseModeEnum::TargetReps->value, 'target_reps' => 50, 'weight' => 30],
                    ['name' => 'Жим штанги стоя', 'sets' => 3, 'reps' => '10', 'weight' => 15],
                    ['name' => 'Подъём штанги на бицепс', 'sets' => 3, 'reps' => '12', 'weight' => 10],
                ],
            ],
            [
                'name'        => 'Стрит-воркаут: турник для начинающих',
                'description' => 'Тренировка на турнике для новичков — спина, бицепс, кор.',
                'type'        => WorkoutProgramTypeEnum::Calisthenics->value,
                'level'       => WorkoutProgramLevelEnum::Beginner->value,
                'exercises'   => [
                    ['name' => 'Подтягивания широким хватом', 'sets' => 4, 'reps' => '6'],
                    ['name' => 'Подтягивания нейтральным хватом', 'sets' => 3, 'reps' => '8'],
                    ['name' => 'Подтягивания узким обратным хватом', 'sets' => 3, 'reps' => '8'],
                    ['name' => 'Подъём ног в висе на турнике', 'sets' => 3, 'reps' => '12'],
                    ['name' => 'Негативные подтягивания', 'sets' => 3, 'reps' => '5'],
                ],
            ],
            [
                'name'        => 'Стрит-воркаут: брусья (продвинутый)',
                'description' => 'Продвинутая тренировка на брусьях — грудь, трицепс, кор.',
                'type'        => WorkoutProgramTypeEnum::Calisthenics->value,
                'level'       => WorkoutProgramLevelEnum::Advanced->value,
                'exercises'   => [
                    ['name' => 'Отжимания на брусьях', 'sets' => 4, 'reps' => '12'],
                    ['name' => 'Отжимания на брусьях узким хватом', 'sets' => 4, 'reps' => '12'],
                    ['name' => 'Уголок на брусьях', 'sets' => 3, 'reps' => '30 сек'],
                    ['name' => 'Подъём коленей к груди на брусьях', 'sets' => 3, 'reps' => '15'],
                    ['name' => 'Выход силой на турнике', 'mode' => ExerciseModeEnum::TargetReps->value, 'target_reps' => 30],
                ],
            ],
        ];

        foreach ($programs as $program) {
            $programId = DB::table('workout_programs')->insertGetId([
                'user_id'     => null,
                'name'        => $program['name'],
                'description' => $program['description'],
                'type'        => $program['type'],
                'level'       => $program['level'],
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            foreach ($program['exercises'] as $order => $pe) {
                DB::table('workout_program_exercises')->insert([
                    'workout_program_id' => $programId,
                    'exercise_id'        => $exerciseIds[$pe['name']],
                    'sets'               => $pe['sets'] ?? 3,
                    'reps'               => $pe['reps'] ?? '10',
                    'weight'             => $pe['weight'] ?? null,
                    'mode'               => $pe['mode'] ?? ExerciseModeEnum::Sets->value,
                    'target_reps'        => $pe['target_reps'] ?? null,
                    'comm'               => '',
                    'order'              => $order,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $programIds = DB::table('workout_programs')
            ->where('is_system', true)
            ->whereIn('name', $this->programNames)
            ->pluck('id');

        DB::table('workout_program_exercises')->whereIn('workout_program_id', $programIds)->delete();
        DB::table('workout_programs')->whereIn('id', $programIds)->delete();

        DB::table('exercises')
            ->where('is_system', true)
            ->whereIn('name', $this->exerciseNames)
            ->delete();
    }
};
