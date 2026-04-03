<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->string('status', 20)->default('saved')->after('post_id');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropColumn('status');
        });
    }
};
