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
        // Add foreign key constraint to report_scores table after student_reports table is created
        if (Schema::hasTable('report_scores') && Schema::hasTable('student_reports')) {
            Schema::table('report_scores', function (Blueprint $table) {
                $table->foreign('report_id')->references('id')->on('student_reports')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('report_scores')) {
            Schema::table('report_scores', function (Blueprint $table) {
                try {
                    $table->dropForeign(['report_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue silently
                }
            });
        }
    }
};