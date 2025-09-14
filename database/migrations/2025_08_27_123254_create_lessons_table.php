<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {

            $table->string('lesson_id', 10)->primary();
            $table->string('tutor_id', 10);
            $table->string('class_id', 10);
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            // Time start to end
            $table->date('active_from')->nullable();
            $table->date('active_until')->nullable();
            $table->string('room', 80)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('tutor_id')
                ->references('tutor_id')->on('tutors')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('class_id')
                ->references('class_id')->on('classes')
                ->cascadeOnUpdate()->cascadeOnDelete();

            // Helpful index for timetable queries
            $table->index(['class_id', 'day_of_week', 'start_time'], 'lessons_class_day_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};