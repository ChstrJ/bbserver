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
            $table->string('reference_number');
            $table->float('amount_due');
            $table->integer('number_of_items');
            $table->integer('payment_method');
            $table->json('checkouts');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->float('commission')->nullable();
            $table->string('image')->nullable();
            $table->date('created_at')->now();
            $table->date('updated_at')->nullable();
            
            $table->foreignId('user_id')
                    ->constrained('users');
            $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('customers');

            $table->softDeletes();
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
