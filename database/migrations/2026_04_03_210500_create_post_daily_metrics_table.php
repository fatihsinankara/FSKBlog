<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('new_bookmarks')->default(0);
            $table->unsignedInteger('new_comments')->default(0);
            $table->timestamps();

            $table->unique(['post_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_daily_metrics');
    }
};
