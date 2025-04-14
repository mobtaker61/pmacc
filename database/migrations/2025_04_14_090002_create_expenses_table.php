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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IRR');
            $table->decimal('rate', 15, 2)->default(1);
            $table->decimal('irr_amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('group_id')->nullable()->constrained('expense_groups')->onDelete('set null');
            $table->foreignId('party_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('petty_cash_box_id')->constrained()->onDelete('cascade');
            $table->string('receipt_image', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
}; 