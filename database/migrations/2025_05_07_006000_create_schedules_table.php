<?php /** 2025_05_07_006000_create_schedules_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tema
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Sub-tema dalam satu tema
        Schema::create('sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('week')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('schedule_details');
        Schema::dropIfExists('schedules');
    }
};
