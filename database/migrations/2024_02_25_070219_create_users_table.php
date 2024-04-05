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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('username');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_logout_at')->nullable();
            $table->timestamps();

            $table->foreignId('role_id')->constrained('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
