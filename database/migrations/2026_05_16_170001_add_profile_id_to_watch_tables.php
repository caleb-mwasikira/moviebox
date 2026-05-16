<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watch_progress', function (Blueprint $table) {
            $table->foreignId('profile_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->dropUnique(['user_id', 'watchable_type', 'watchable_id']);
            $table->unique(['profile_id', 'watchable_type', 'watchable_id'], 'wp_profile_watchable_unique');
        });

        Schema::table('watchlist_items', function (Blueprint $table) {
            $table->foreignId('profile_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->dropUnique(['user_id', 'watchable_type', 'watchable_id']);
            $table->unique(['profile_id', 'watchable_type', 'watchable_id'], 'wi_profile_watchable_unique');
        });
    }

    public function down(): void
    {
        Schema::table('watch_progress', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropUnique('wp_profile_watchable_unique');
            $table->dropColumn('profile_id');
            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
        });

        Schema::table('watchlist_items', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropUnique('wi_profile_watchable_unique');
            $table->dropColumn('profile_id');
            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
        });
    }
};
