<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_program_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sets')->default(3);
            $table->string('reps')->default('10');
            $table->decimal('weight', 5, 2)->nullable(); // null = только вес тела
            $table->string('comm');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_program_exercises');
    }
};