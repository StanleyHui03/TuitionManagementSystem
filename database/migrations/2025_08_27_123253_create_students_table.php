<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_id', 10)->primary();          // e.g. S0001
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('studentName', 150);
            $table->string('phoneNum', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('gender', 20)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
          ->cascadeOnUpdate()->cascadeOnDelete();

        });
    }
    public function down(): void { Schema::dropIfExists('students'); }
};