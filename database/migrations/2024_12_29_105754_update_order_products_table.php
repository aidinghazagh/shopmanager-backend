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
        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedInteger('price_on_created')->after('product_id');
            $table->unsignedInteger('purchase_price_on_created')->after('price_on_created');
            $table->unsignedInteger('quantity')->default(1)->after('purchase_price_on_created');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('price_on_created');
            $table->dropColumn('purchase_price_on_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
