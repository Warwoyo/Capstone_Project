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
        Schema::create('reports', function (Blueprint $table) {
            $table->enum('semester', ['Ganjil','Genap']);
            $table->string('tema_kode');      // no-kode tema penilaian
            $table->string('tema');
            $table->string('sub_tema_kode');  // no-kode sub-tema penilaian
            $table->string('sub_tema');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
