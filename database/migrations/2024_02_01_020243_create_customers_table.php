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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('email_address');
            $table->string('address');
            $table->timestamps();
            $table->tinyInteger('added_by')->constrained('user_id')->on('users')->nullable();
            $table->tinyInteger('updated_by')->constrained('user_id')->on('users')->nullable();

            // $table->foreignId('user_id')
            //     ->constrained('users')
            //     ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
