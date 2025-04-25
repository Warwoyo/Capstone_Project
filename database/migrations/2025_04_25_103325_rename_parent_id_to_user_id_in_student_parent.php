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
        Schema::table('student_parent', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'user_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('student_parent', function (Blueprint $table) {
            $table->renameColumn('user_id', 'parent_id');
        });
    }
    
};
