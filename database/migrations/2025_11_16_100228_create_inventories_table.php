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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->unsignedInteger('stock_level')->default(0)->index();
            $table->unsignedInteger('reserved_stock')->default(0)->index();
            $table->unsignedInteger('available_stock')->default(0)->index();
            $table->decimal('unit_cost', 10, 2)->index();
            $table->foreign('sku')->references('sku')->on('products')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
