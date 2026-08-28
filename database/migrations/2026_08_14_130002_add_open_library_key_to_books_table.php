<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('open_library_key')->nullable()->after('user_id');
        });
    }
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('open_library_key');
        });
    }
};
