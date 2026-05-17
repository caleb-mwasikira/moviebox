<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('KSH');
            $table->unsignedInteger('price');
            $table->enum('interval', ['week', 'month', 'year']);

            // Features
            $table->unsignedInteger('trial_period_days')->default(0);
            $table->integer('max_screens')->default(1);
            $table->string('max_resolution')->default('1080p');
            $table->boolean('has_ads')->default(false);
            $table->boolean('download_enabled')->default(true);

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
