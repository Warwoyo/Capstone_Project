<?php /** 2025_05_07_008000_create_registration_tokens_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registration_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->char('token', 8)->unique();
            $t->timestamp('expires_at');
            $t->timestamp('used_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('registration_tokens'); }
};
