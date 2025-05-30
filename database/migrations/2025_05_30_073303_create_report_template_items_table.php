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
        Schema::create('report_template_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('template_id')->constrained('report_templates')->cascadeOnDelete();
            $t->string('kode');                // 1 / 1.1 / 1.1.1 …
            $t->string('label');               // “NILAI AGAMA …” / kompetensi
            $t->unsignedTinyInteger('order')->default(0);
            $t->foreignId('parent_id')->nullable()
                ->constrained('report_template_items')->cascadeOnDelete(); // nesting
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_template_items');
    }
};
