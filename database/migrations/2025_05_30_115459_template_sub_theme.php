<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('template_themes')->cascadeOnDelete();
            $table->string('code');                    // Kode sub-tema (ST01, ST02, etc.)
            $table->string('name');                    // Nama sub-tema
            $table->integer('order')->default(0);     // Urutan sub-tema
            $table->timestamps();
            
            $table->unique(['theme_id', 'code']);      // Kode sub-tema unik per tema
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_sub_themes');
    }
};