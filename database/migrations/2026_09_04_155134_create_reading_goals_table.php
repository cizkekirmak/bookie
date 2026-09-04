<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year'); // Örn: 2026
            $table->unsignedSmallInteger('target_books'); // Kullanıcının koyduğu hedef (örn: 15)
            $table->timestamps();

            // Bir kullanıcı aynı yıl için sadece 1 kayıt açabilsin (hile önleme)
            $table->unique(['user_id', 'year']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reading_goals');
    }
};
