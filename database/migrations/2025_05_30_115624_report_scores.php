<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id'); // Don't use foreignId yet
            $table->foreignId('sub_theme_id')->constrained('template_sub_themes')->cascadeOnDelete();
            $table->enum('score', ['BM', 'MM', 'BSH', 'BSB'])->nullable(); // Belum Muncul, Mulai Muncul, Berkembang Sesuai Harapan, Berkembang Sangat Baik
            $table->text('notes')->nullable();         // Catatan untuk sub-tema ini
            $table->timestamps();
            
            $table->unique(['report_id', 'sub_theme_id']); // Satu nilai per sub-tema per rapor
            $table->index('report_id'); // Add index for the foreign key we'll add later
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_scores');
    }
};