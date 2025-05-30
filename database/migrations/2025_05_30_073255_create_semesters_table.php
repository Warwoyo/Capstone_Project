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
        Schema::create('semesters', function (Blueprint $t) {
            $t->id();
            $t->string('name');            // Ganjil / Genap / dst
            $t->string('year');            // 2024/2025
            $t->string('timeline')->nullable();  // Agu–Des 2024
            $t->text('description')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
