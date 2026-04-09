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
            // Recording Info
            $table->string('instrument_no')->nullable()->after('shipping_notes');
            $table->string('book')->nullable()->after('instrument_no');
            $table->string('page')->nullable()->after('book');
            $table->date('recorded_at')->nullable()->after('page');
            $table->decimal('recording_fee', 10, 2)->nullable()->after('recorded_at');

            // Return to Partner Info
            $table->string('return_courier')->nullable()->after('recording_fee');
            $table->string('return_tracking_no')->nullable()->after('return_courier');
            $table->date('returned_at')->nullable()->after('return_tracking_no');
            $table->text('return_notes')->nullable()->after('returned_at');

            // Indexes for fast lookup
            $table->index('instrument_no');
            $table->index('return_tracking_no');
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn([
                'instrument_no', 'book', 'page', 'recorded_at', 'recording_fee',
                'return_courier', 'return_tracking_no', 'returned_at', 'return_notes'
            ]);
        });
    }
};
