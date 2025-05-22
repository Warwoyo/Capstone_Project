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
        Schema::table('attendances', function (Blueprint $t) {
            $t->foreignId('schedule_id')
            ->nullable()                       // boleh kosong (optional)
            ->constrained('schedules')
            ->cascadeOnDelete();

            $t->string('description', 200)->nullable();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable()->after('id');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};
