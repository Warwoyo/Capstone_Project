<?php /** 2025_05_07_000100_create_user_contacts_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_contacts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('phone_number', 20)->unique();
            $t->boolean('is_primary')->default(false);
            $t->timestamp('verified_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { 
        Schema::dropIfExists('user_contacts'); 
    }
};
