<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_scores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $t->foreignId('template_item_id')->constrained('report_template_items')->cascadeOnDelete();
            $t->enum('value', ['BM','MM','BSH','BSB'])->nullable();
            $t->text('note')->nullable();
            $t->timestamps();
            $t->unique(['report_id','template_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_scores');
    }
};
