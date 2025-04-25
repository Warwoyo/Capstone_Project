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
        Schema::create('student_parent', function (Blueprint $table) {
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('parent_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
    
            $table->primary(['student_id', 'parent_id']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('student_parent');
    }
    
};
