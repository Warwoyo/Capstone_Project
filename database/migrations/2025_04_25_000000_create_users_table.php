<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique()->nullable();
            $table->string('phone_number', 20)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'parent']);
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
