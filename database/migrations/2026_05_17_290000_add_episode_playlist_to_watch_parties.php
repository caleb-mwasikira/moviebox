<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watch_parties', function (Blueprint $table) {
            $table->unsignedBigInteger('current_episode_id')->nullable()->after('starts_at');
            $table->foreign('current_episode_id')->references('id')->on('episodes')->nullOnDelete();
        });

        Schema::create('watch_party_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('episode_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['watch_party_id', 'episode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_party_episodes');
        Schema::table('watch_parties', function (Blueprint $table) {
            $table->dropForeign(['current_episode_id']);
            $table->dropColumn('current_episode_id');
        });
    }
};
