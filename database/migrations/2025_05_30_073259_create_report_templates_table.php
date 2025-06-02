<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');                           // Judul template
            $table->text('description')->nullable();           // Deskripsi template
            $table->enum('semester_type', ['ganjil', 'genap']); // Jenis semester
            $table->boolean('is_active')->default(true);       // Status aktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};