<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_user', function (Blueprint $t) {
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->primary(['student_id','user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('student_user'); }
};
