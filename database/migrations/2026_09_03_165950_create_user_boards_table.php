<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Panodaki tüm post-it'ler ve sticker'lar (JSON array)
            $table->json('board_items')->nullable();
            // 9 adet kancada asılı olan anahtarlık key'leri (JSON array, örn: ["maymun", null, "jake", ...])
            $table->json('hook_slots')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_boards');
    }
};