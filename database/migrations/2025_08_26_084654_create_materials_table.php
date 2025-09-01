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
        Schema::create('materials', function (Blueprint $table) {
            $table->string('material_id', 10)->primary();         // e.g. M0001
            $table->string('tutor_id', 10)->nullable();
            $table->string('subject_id', 10);
            $table->string('title', 200);
            $table->string('file_path', 255);
            $table->string('original_file_name', 200)->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('tutor_id')->on('tutors')
                  ->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('subject_id')->references('subject_id')->on('subjects')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
