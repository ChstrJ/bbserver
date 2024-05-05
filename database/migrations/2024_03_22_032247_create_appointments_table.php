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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->date('date');
            $table->enum('status', ['pending', 'cancelled', 'reschedule', 'confirmed', 'completed'])->default('pending');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
