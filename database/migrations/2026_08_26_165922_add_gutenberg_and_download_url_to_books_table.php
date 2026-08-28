<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('gutenberg_id')->nullable()->after('open_library_key')->index();
            $table->string('download_url')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('gutenberg_id');
            $table->dropColumn('download_url');
        });
    }
};
