<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_session_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->nullable()->constrained()->nullOnDelete();
            $table->string('exercise_name'); // снапшот названия упражнения
            $table->unsignedTinyInteger('planned_sets')->default(3);
            $table->string('planned_reps');  // varchar — может быть "8-12" или "до отказа"
            $table->string('comm')->default('');  // varchar — может быть "8-12" или "до отказа"
            $table->decimal('planned_weight', 5, 2)->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_session_exercises');
    }
};