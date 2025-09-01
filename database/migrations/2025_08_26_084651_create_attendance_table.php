<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->string('attendance_id', 10)->primary();       // e.g. A0001
            $table->string('marked_by_tutor_id', 10)->nullable();
            $table->string('lesson_id', 10);
            $table->string('student_id', 10);
            $table->string('status', 40)->default('present');     // present/absent/late/...
            $table->timestamps();

            $table->foreign('marked_by_tutor_id')->references('tutor_id')->on('tutors')
                  ->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('lesson_id')->references('lesson_id')->on('lessons')
                  ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('student_id')->references('student_id')->on('students')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['lesson_id', 'student_id'], 'uniq_attendance_lesson_student');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
