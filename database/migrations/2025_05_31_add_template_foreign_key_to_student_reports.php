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
        // Check if student_reports table exists before adding foreign key
        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                // Check which template table exists and add appropriate foreign key
                if (Schema::hasTable('report_templates')) {
                    $table->foreign('template_id')->references('id')->on('report_templates')->onDelete('cascade');
                } elseif (Schema::hasTable('rapor_templates')) {
                    $table->foreign('template_id')->references('id')->on('rapor_templates')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                // Check if foreign key exists before dropping
                if (Schema::hasColumn('student_reports', 'template_id')) {
                    try {
                        $table->dropForeign(['template_id']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist, continue silently
                    }
                }
            });
        }
    }
};