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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('orderTransactionID');
            $table->unsignedBigInteger('userID');
            $table->date('orderTransactionDate')->default(date('Y-m-d'));
            $table->time('orderTransactionTime')->default(date('H:i:s'));
            $table->float('amountDue');
            $table->integer('numberOfItems');
            $table->string('paymentType');
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
