<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->string('enrollment_id', 10)->primary();       // e.g. E0001
            $table->string('class_id', 10);
            $table->string('student_id', 10);
            $table->timestamps();

            $table->foreign('class_id')->references('class_id')->on('classes')
                  ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('student_id')->references('student_id')->on('students')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['class_id', 'student_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('enrollments'); }
};
