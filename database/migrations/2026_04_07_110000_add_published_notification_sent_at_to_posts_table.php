<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('published_notification_sent_at')->nullable()->after('published_at');
            $table->index('published_notification_sent_at');
        });

        DB::table('posts')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update([
                'published_notification_sent_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['published_notification_sent_at']);
            $table->dropColumn('published_notification_sent_at');
        });
    }
};
