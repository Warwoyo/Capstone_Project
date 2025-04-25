<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registration_tokens', function (Blueprint $table) {
            $table->char('token', 8)
                  ->after('student_id')
                  ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('registration_tokens', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
