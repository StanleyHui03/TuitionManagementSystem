<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subjects', function (Blueprint $table) {
            $table->string('subject_id', 10)->primary();          // e.g. SU0001
            $table->string('subject_Name', 150);
            $table->text('subject_Description')->nullable();
            $table->unsignedInteger('duration_Hours')->default(0);
            $table->decimal('subject_Fee', 10, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subjects'); }
};
