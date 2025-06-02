<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parent_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->enum('relation', ['father', 'mother']);
            $t->string('nik', 20)->nullable();
            $t->string('occupation', 100)->nullable();
            $t->text('address')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('parent_profiles'); }
};
