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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Customer::class)->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('discount')->nullable();
            $table->unsignedInteger('price_on_created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
