<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->cascadeOnDelete();
            $table->string('code');                    // Kode tema (T01, T02, etc.)
            $table->string('name');                    // Nama tema
            $table->integer('order')->default(0);     // Urutan tema
            $table->timestamps();
            
            $table->unique(['template_id', 'code']);   // Kode tema unik per template
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_themes');
    }
};