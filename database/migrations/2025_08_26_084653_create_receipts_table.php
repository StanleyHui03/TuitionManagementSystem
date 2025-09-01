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
        Schema::create('receipts', function (Blueprint $table) {
            $table->string('receipt_id', 10)->primary();          // e.g. R0001
            $table->string('payment_id', 10)->unique();
            $table->decimal('subTotal', 10, 2)->default(0);
            $table->date('receiptDate')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('payment_id')->on('payments')
                  ->cascadeOnUpdate()->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
