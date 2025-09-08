<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('class_id', 10)->primary();            // e.g. C0001
            $table->string('subject_id', 10);
            $table->timestamps();

            $table->foreign('subject_id')->references('subject_id')->on('subjects')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('classes'); }
};
