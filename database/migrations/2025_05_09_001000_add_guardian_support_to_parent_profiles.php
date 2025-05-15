<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuardianSupportToParentProfiles extends Migration
{
    public function up()
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->enum('relation', ['father', 'mother', 'guardian'])->change();
        });
    }

    public function down()
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->enum('relation', ['father', 'mother'])->change();
        });
    }
}