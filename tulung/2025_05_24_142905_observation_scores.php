<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_detail_id')->constrained()->cascadeOnDelete();
            $table->integer('score'); // 1-4 scale
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'schedule_detail_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observation_scores');
    }
};