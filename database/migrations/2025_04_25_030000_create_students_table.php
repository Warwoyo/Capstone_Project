<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nik', 20)
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->text('address')->nullable()
            $table->unsignedBigInteger('parent_id');
            $table->string('photo')->nullable(); // Bisa null, biar aman kalau belum upload
            $table->text('medical_history')->nullable()
            $table->string('group')->nullable(); // Nama kelompok, misal A/B/C, opsional juga
            $table->timestamps();
            
            // Foreign key ke tabel users yang punya role parent
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
