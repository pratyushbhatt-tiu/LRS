<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the import_logs table which tracks every CSV bulk import job.
     */
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();

            // Who triggered the import
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // The original uploaded filename and its storage path
            $table->string('filename');
            $table->string('file_path');

            // Import job lifecycle status: pending → processing → done / failed
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->index('status');

            // Row counts for the summary page
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('success_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            // JSON array of failed rows: [ { row, field, message, raw_data } ]
            $table->longText('errors')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
