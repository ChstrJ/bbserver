<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->tinyInteger('payment_method');
            $table->json('checkouts');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->float('commission')->nullable();
            $table->timestamps();
            
            $table->foreignId('user_id')
                    ->constrained('users');
            $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('customers');

            $table->boolean('is_removed')->default(false);
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_commision CHECK(commission >= 0)');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
