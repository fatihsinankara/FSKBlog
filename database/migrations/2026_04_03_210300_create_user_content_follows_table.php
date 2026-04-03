<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_content_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('followable');
            $table->timestamps();

            $table->unique(['user_id', 'followable_type', 'followable_id'], 'user_content_follows_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_content_follows');
    }
};
