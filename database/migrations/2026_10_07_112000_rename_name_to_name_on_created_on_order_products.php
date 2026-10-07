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
        // add_name_on_created_to_order_products created the column as `name`, but the code writes `name_on_created`
        if (Schema::hasColumn('order_products', 'name') && ! Schema::hasColumn('order_products', 'name_on_created')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->renameColumn('name', 'name_on_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
