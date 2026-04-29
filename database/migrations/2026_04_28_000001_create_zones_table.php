<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('oblast_name');
            $table->decimal('bbox_west',  8, 4);
            $table->decimal('bbox_south', 8, 4);
            $table->decimal('bbox_east',  8, 4);
            $table->decimal('bbox_north', 8, 4);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
