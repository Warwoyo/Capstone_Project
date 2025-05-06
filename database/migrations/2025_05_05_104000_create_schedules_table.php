<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->string('title',100);                     // nama tema
            $t->date('start_date');
            $t->timestamps();
        });

        Schema::create('schedule_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('week');             // Minggu ke-
            $t->text('description');                    // pembahasan
            $t->timestamps();
            $t->unique(['schedule_id','week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
        Schema::dropIfExists('schedules');
    }
};
