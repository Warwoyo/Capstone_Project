<?php /** 2025_05_07_007000_create_observations_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('observations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('schedule_detail_id')->constrained('sub_themes')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->text('description');
            $t->timestamps();
            $t->unique(['schedule_detail_id', 'student_id']); // satu catatan per siswa & sub-tema
        });
    }
    public function down(): void { Schema::dropIfExists('observations'); }
};
