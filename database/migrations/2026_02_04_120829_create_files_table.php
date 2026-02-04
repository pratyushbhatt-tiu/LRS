<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_no')->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('doc_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('recording_purpose_id')->constrained()->restrictOnDelete();
            $table->foreignId('state_id')->constrained()->restrictOnDelete();
            $table->foreignId('county_id')->constrained()->restrictOnDelete();
            $table->string('partner_ref_no')->nullable();
            $table->date('received_date');
            $table->string('current_status');
            $table->timestamps();

            $table->index('file_no');
            $table->index('client_id');
            $table->index('current_status');
            $table->index('received_date');
            $table->index('partner_ref_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
