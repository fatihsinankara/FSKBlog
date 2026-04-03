<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('FSK Blog');
            $table->text('site_description')->nullable();
            $table->text('site_keywords')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('default_meta_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->string('default_og_image')->nullable();
            $table->longText('custom_head_code')->nullable();
            $table->longText('custom_body_end_code')->nullable();
            $table->boolean('maintenance_enabled')->default(false);
            $table->string('maintenance_title')->nullable();
            $table->text('maintenance_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
