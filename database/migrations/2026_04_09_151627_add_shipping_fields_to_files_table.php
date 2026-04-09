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
        Schema::table('files', function (Blueprint $table) {
            $table->string('courier')->nullable()->after('current_status');
            $table->string('tracking_number')->nullable()->after('courier');
            $table->date('shipped_at')->nullable()->after('tracking_number');
            $table->text('shipping_notes')->nullable()->after('shipped_at');

            // Index for faster lookups
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex(['tracking_number']);
            $table->dropColumn(['courier', 'tracking_number', 'shipped_at', 'shipping_notes']);
        });
    }
};
