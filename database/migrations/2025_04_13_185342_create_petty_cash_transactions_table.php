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
        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_box_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->enum('type', ['income', 'expense']);
            $table->string('payer_receiver', 191);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IRR');
            $table->decimal('rate', 15, 2)->default(1);
            $table->decimal('irr_amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('receipt_image', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
