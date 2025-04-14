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
            $table->string('type'); // income or expense
            $table->string('payer_receiver');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency');
            $table->decimal('irr_amount', 15, 2)->nullable();
            $table->string('receipt_image')->nullable();
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
