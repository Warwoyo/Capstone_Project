<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->timestamp('issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // opsional: pastikan satu student satu template sekali saja
            $table->unique(['classroom_id','student_id','template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};