<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateClassroomsTable extends Migration
{
    public function up(): void 
    {
        Schema::create('classrooms', function (Blueprint $t) {
            $t->id();
            $t->string('name', 50);
            $t->text('description')->nullable();                     
            $t->foreignId('owner_id')                
              ->constrained('users')->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('classroom_student', function (Blueprint $t) {
            $t->foreignId('classroom_id')
              ->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')
              ->constrained()->cascadeOnDelete();
            $t->primary(['classroom_id','student_id']);
        });
    }

    public function down(): void 
    {
        Schema::dropIfExists('classroom_student');
        Schema::dropIfExists('classrooms');
    }
}