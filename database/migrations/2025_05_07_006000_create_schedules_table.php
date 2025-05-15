<?php /** 2025_05_07_006000_create_schedules_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tema
        Schema::create('schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->string('title', 100);         // judul tema
            $t->timestamps();
        });

        // Sub-tema dalam satu tema
        Schema::create('schedule_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $t->string('sub_title', 100);
            $t->date('start_date');
            $t->date('end_date');
            $t->unsignedTinyInteger('week')->nullable(); // opsional, minggu ke-X
            $t->timestamps();
            $t->unique(['schedule_id', 'sub_title']);    // hindari duplikat
        });
    }
    public function down(): void {
        Schema::dropIfExists('schedule_details');
        Schema::dropIfExists('schedules');
    }
};
