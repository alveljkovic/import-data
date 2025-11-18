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
            $table->date('order_date')->index();
            $table->string('channel')->index();
            $table->string('sku')->index();
            $table->string('item_description')->nullable();
            $table->string('origin')->index();
            $table->string('so_num')->index();
            $table->decimal('cost', 10, 2)->default(0)->index();
            $table->decimal('shipping_cost', 10, 2)->default(0)->index();
            $table->decimal('total_price', 10, 2)->default(0)->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['so_num', 'sku']);
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
