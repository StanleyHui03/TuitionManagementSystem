<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tutors', function (Blueprint $table) {
            $table->string('tutor_id', 10)->primary();            // e.g. T0001
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('tutor_name', 150);
            $table->string('phoneNumber', 30)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
          ->cascadeOnUpdate()->cascadeOnDelete();

        });
    }
    public function down(): void { Schema::dropIfExists('tutors'); }
};

