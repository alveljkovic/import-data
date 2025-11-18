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
        Schema::create('data_import_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_import_log_id');
            $table->foreign('data_import_log_id')
                ->references('id')
                ->on('data_import_logs')
                ->onDelete('cascade');
            $table->string('table_name');
            $table->unsignedBigInteger('row_id');
            $table->integer('row_number');
            $table->string('column_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['table_name', 'row_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_import_audits');
    }
};
