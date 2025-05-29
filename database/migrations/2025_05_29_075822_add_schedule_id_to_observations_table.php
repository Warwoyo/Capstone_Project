<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('observations', function (Blueprint $table) {
            // letakkan tepat setelah primary key supaya rapi
            $table->foreignId('schedule_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('schedule_id');
        });
    }
};
