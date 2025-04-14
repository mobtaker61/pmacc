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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_group_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('company_name')->nullable()->after('last_name');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable()->after('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
