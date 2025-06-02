<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teacher_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('nip', 20)->nullable();
            $t->text('address')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('teacher_profiles'); }
};
