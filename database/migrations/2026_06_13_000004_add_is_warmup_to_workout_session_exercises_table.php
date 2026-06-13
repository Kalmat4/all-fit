<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_session_exercises', function (Blueprint $table) {
            $table->boolean('is_warmup')->default(false)->after('target_reps');
        });
    }

    public function down(): void
    {
        Schema::table('workout_session_exercises', function (Blueprint $table) {
            $table->dropColumn('is_warmup');
        });
    }
};
