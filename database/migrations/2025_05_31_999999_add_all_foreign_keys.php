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
        // Add foreign key constraints after all tables are created
        
        // 1. Add template foreign key to student_reports
        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                if (Schema::hasTable('report_templates')) {
                    try {
                        $table->foreign('template_id')->references('id')->on('report_templates')->onDelete('cascade');
                    } catch (\Exception $e) {
                        // Foreign key might already exist
                    }
                }
            });
        }
        
        // 2. Add report foreign key to report_scores
        if (Schema::hasTable('report_scores') && Schema::hasTable('student_reports')) {
            Schema::table('report_scores', function (Blueprint $table) {
                try {
                    $table->foreign('report_id')->references('id')->on('student_reports')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys
        if (Schema::hasTable('report_scores')) {
            Schema::table('report_scores', function (Blueprint $table) {
                try {
                    $table->dropForeign(['report_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }
        
        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                try {
                    $table->dropForeign(['template_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }
    }
};