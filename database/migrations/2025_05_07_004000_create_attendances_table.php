<?php /** 2025_05_07_004000_create_attendances_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->date('attendance_date');
            $t->enum('status', ['hadir', 'sakit', 'ijin', 'alpha'])->default('hadir');
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->unique(['attendance_date', 'classroom_id', 'student_id']);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('attendances'); }
};
