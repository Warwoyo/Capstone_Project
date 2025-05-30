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
        Schema::create('class_report_template', function (Blueprint $t) {
            $t->foreignId('class_id')->constrained('classrooms')->cascadeOnDelete();
            $t->foreignId('template_id')->constrained('report_templates')->cascadeOnDelete();
            $t->date('assigned_at')->default(now());
            $t->boolean('is_current')->default(true);
            $t->primary(['class_id', 'template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_report_template');
    }
};
