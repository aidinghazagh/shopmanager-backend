<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete(); // Links to the product
            $table->enum('action', ['created', 'updated', 'deleted']); // 'created', 'updated', or 'deleted'
            $table->string('changed_field')->nullable(); // Field that was changed
            $table->string('old_value')->nullable(); // Old value of the field
            $table->string('new_value')->nullable(); // New value of the field
            $table->timestamp('logged_at')->default(DB::raw('CURRENT_TIMESTAMP')); // Log timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_logs');
    }
};
