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
        Schema::create('payments', function (Blueprint $table) {
            $table->string('payment_id', 10)->primary();          // e.g. P0001
            $table->string('student_id', 10);
            $table->decimal('paymentTotal', 10, 2)->default(0);
            $table->date('paymentDate')->nullable();
            $table->string('status', 40)->default('pending');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
