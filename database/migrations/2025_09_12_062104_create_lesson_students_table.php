<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lesson_students', function (Blueprint $table) {
            // Composite key
            $table->string('lesson_id', 10);
            $table->string('student_id', 10);

            // Optional metadata
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            // Constraints
            $table->primary(['lesson_id', 'student_id']); // or use unique() if you prefer

            $table->foreign('lesson_id')
                ->references('lesson_id')->on('lessons')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_students');
    }
};