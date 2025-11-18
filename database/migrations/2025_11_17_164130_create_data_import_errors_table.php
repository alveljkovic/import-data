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
        Schema::create('data_import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_import_log_id')
                  ->constrained('data_import_logs')
                  ->onDelete('cascade');
            $table->unsignedInteger('row_number')->index();
            $table->string('column_name')->index();
            $table->text('value')->nullable();
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_import_errors');
    }
};
