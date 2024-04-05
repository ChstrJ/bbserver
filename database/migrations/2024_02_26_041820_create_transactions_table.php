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
            $table->id();
            $table->json('customer')->nullable();
            $table->float('amount_due');
            $table->integer('number_of_items');
            $table->integer('payment_method');
            $table->json('checkouts');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('customers')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
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
