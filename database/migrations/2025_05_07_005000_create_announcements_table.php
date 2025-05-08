<?php /** 2025_05_07_005000_create_announcements_table.php */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->string('title', 100);
            $t->text('description');
            $t->string('image')->nullable();          // path di storage
            $t->date('published_at')->default(now());
            $t->date('visible_until')->nullable();    // opsional
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('announcements'); }
};
