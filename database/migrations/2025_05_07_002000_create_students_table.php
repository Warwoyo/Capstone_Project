<?php /** 2025_05_07_002000_create_students_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->string('student_number', 30)->unique();  // no induk
            $t->string('name', 100);
            $t->date('birth_date');
            $t->enum('gender', ['male', 'female'])->default('male');
            $t->string('photo')->nullable();
            $t->text('medical_history')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('students'); }
};
