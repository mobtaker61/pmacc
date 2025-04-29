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
        Schema::create('petty_cash_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('description', 191)->nullable();
            $table->string('currency', 3)->default('IRR');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('try_rate', 15, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_boxes');
    }
};
