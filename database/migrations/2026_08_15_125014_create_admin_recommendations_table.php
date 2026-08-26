<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string("book_key");
            $table->string("cover_url")->nullable();
            $table->string("authors")->nullable();
            $table->text("admin_note")->nullable();
            $table->boolean("is_active")->default(true);
            $table->string("title");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_recommendations');
    }
};
