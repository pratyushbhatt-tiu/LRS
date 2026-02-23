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
        Schema::create('fee_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('doc_type_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('county_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('rule_name');
            $table->decimal('base_fee', 10, 2);
            $table->decimal('per_page_fee', 10, 2)->nullable();
            $table->decimal('minimum_fee', 10, 2)->nullable();
            $table->decimal('maximum_fee', 10, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for efficient rule matching
            $table->index(['client_id', 'doc_type_id', 'state_id', 'county_id'], 'fee_rules_matching_index');
            $table->index('priority');
            $table->index('active');
            $table->index(['effective_from', 'effective_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_rules');
    }
};
