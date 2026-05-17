<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('episode_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_path')->nullable()->change();
            $table->string('streaming_path')->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->boolean('is_free')->default(false);
            $table->timestamps();

            $table->unique(['season_id', 'episode_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
