<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyllabusesTable extends Migration
{
    public function up(): void
    {
        Schema::create('syllabuses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('title',100);
            $t->text('description');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabuses');
    }
};
