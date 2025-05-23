<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registration_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')->cascadeOnDelete();
            $table->char('token', 8)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('registration_tokens');
    }
};
