<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('quantity');
            $table->float('srp');
            $table->float('member_price');
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
            $table->tinyInteger('added_by')->nullable();
            $table->tinyInteger('updated_by')->nullable();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnUpdate();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
