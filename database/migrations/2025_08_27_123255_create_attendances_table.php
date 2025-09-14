<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance', function (Blueprint $table) {

            $table->string('attendance_id', 10)->primary();
            $table->string('marked_by_tutor_id', 10)->nullable();
            $table->string('lesson_id', 10);
            $table->string('student_id', 10);

            $table->string('status', 40)->default('present'); // present/absent/late/...
            $table->date('session_date');                     // date of the class occurrence
            $table->dateTime('marked_at')->nullable();        // when the tutor saved
            $table->string('note', 255)->nullable();          // optional remarks
            $table->timestamps();

            $table->foreign('marked_by_tutor_id')
                  ->references('tutor_id')->on('tutors')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
            $table->foreign('lesson_id')
                  ->references('lesson_id')->on('lessons')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Unique: one attendance record per student per lesson per day
            $table->unique(['lesson_id','student_id','session_date'], 'attendance_uq_lesson_student_date');
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance');
    }
};