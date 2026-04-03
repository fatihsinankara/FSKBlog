<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('part_number');
            $table->timestamps();

            $table->unique(['collection_id', 'post_id']);
            $table->unique(['collection_id', 'part_number']);
            $table->unique('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_post');
    }
};
