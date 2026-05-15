<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('watchable_type');   // 'movie' | 'episode'
            $table->unsignedBigInteger('watchable_id');
            $table->float('position')->default(0);   // seconds
            $table->float('duration')->default(0);   // seconds
            $table->timestamp('watched_at');
            $table->timestamps();

            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
            $table->index(['user_id', 'watched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_progress');
    }
};
