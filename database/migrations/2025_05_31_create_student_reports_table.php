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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('template_id');
            $table->json('scores')->nullable(); // Store student scores for each sub-theme
            $table->text('teacher_comment')->nullable();
            $table->text('parent_comment')->nullable();
            $table->json('physical_data')->nullable(); // Store height, weight, head_circumference
            $table->json('attendance_data')->nullable(); // Store attendance summary
            $table->json('theme_comments')->nullable(); // Store comments for each theme
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            // Skip template foreign key for now - we'll add it in a separate migration
            // after confirming the correct table name

            // Unique constraint to prevent duplicate reports
            $table->unique(['classroom_id', 'student_id', 'template_id'], 'unique_student_template_report');

            // Indexes for better performance
            $table->index('classroom_id');
            $table->index('student_id'); 
            $table->index('template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};