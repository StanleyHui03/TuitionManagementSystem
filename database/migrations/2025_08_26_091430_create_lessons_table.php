<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->string('lesson_id', 10)->primary();           // e.g. L0001
            $table->string('tutor_id', 10);
            $table->string('class_id', 10);
            $table->dateTime('start_at');
            $table->dateTime('ends_at');
            $table->string('room', 80)->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('tutor_id')->on('tutors')
                  ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('class_id')->references('class_id')->on('classes')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->index(['class_id', 'start_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('lessons'); }
};
