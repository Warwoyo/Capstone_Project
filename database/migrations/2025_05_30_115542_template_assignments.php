<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->date('assigned_at')->default(now());
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            
            $table->unique(['template_id', 'classroom_id']); // Satu template per kelas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_assignments');
    }
};