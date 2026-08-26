<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->onDelete('cascade');
            $table->string('title');
            $table->string('open_library_key')->nullable();
            $table->string('author');
            $table->text("review")->nullable();
            $table->integer('rating')->default(0);
            $table->string('cover_image')->nullable();
            $table->string('genre')->nullable();
            $table->date('publication_date')->nullable();
            $table->string("status")->default("unread");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
