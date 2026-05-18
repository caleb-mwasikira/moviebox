<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watch_party_members', function (Blueprint $table) {
            $table->timestamp('kicked_at')->nullable()->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('watch_party_members', function (Blueprint $table) {
            $table->dropColumn('kicked_at');
        });
    }
};
