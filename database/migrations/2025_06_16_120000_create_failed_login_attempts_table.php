<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // email or phone number
            $table->string('ip_address');
            $table->timestamp('attempted_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_login_attempts');
    }
};