<?php

use Illuminate\Database\Migrations\Migration;
// database/migrations/2025_05_30_000000_add_score_and_fields_to_observations_table.php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('observations', function (Blueprint $t) {
            $t->tinyInteger('score')->after('description');          // 1-4
            $t->text('observation_text')->nullable()->after('score');
            $t->timestamp('observed_at')->nullable()->after('observation_text');
            $t->foreignId('observer_id')->nullable()
              ->constrained('users')->nullOnDelete()->after('observed_at');
        });
    }
    public function down(): void {
        Schema::table('observations', function (Blueprint $t) {
            $t->dropConstrainedForeignId('observer_id');
            $t->dropColumn(['score', 'observation_text', 'observed_at']);
        });
    }
};
