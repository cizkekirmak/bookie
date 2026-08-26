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
        Schema::create('user_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->onDelete('cascade');
            $table->foreignId('book_id')->constrained("books")->onDelete('cascade');
            $table->enum('status', ['toRead', 'reading', 'read'])->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'book_id']);
            $table->tinyInteger('rating')->nullable();
            $table->text('review')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_books');
    }
};
