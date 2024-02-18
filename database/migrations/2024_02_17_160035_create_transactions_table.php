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
            $table->uuid('id')->unique();
            $table->foreignUuid('from')->references('id')
                ->on('users');
            $table->foreignUuid('to')->references('id')
                ->on('users');
            $table->decimal('value', 10, 2);
            $table->enum('type', ['deposit','transfer','payment']);
            $table->enum('status', ['pending','accepted','refused','canceled']);
            $table->timestamps();
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
