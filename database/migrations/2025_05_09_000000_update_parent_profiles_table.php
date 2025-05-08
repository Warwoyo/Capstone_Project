<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('parent_profiles');        

        Schema::create('parent_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();   // koneksi awal
            $t->string('name', 100);
            $t->enum('relation', ['father', 'mother', 'guardian']);
            $t->string('phone', 20)->nullable()->unique();
            $t->string('email', 100)->nullable()->unique();
            $t->string('nik', 20)->nullable();
            $t->string('occupation', 100)->nullable();
            $t->text('address')->nullable();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
            $t->unique(['student_id','relation']);      // satu ayah/ibu/wali per siswa
        });
    }
    public function down(): void { Schema::dropIfExists('parent_profiles'); }
};
