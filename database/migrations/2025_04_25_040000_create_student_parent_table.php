<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_parent', function (Blueprint $table) {
            $table->foreignId('student_id')
                  ->constrained('students')->cascadeOnDelete();
        
            // Ganti parent_id → user_id
            $table->foreignId('user_id')
                  ->constrained('users')->cascadeOnDelete();
        
            // Sesuaikan primary key-nya
            $table->primary(['student_id','user_id']);
        });
        
    }
    public function down(): void {
        Schema::dropIfExists('student_parent');
    }
};
