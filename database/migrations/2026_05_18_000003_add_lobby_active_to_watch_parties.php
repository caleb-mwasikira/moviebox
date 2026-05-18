<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watch_parties', function (Blueprint $table) {
            $table->boolean('lobby_active')->default(true)->after('ended_at');
        });
    }

    public function down(): void
    {
        Schema::table('watch_parties', function (Blueprint $table) {
            $table->dropColumn('lobby_active');
        });
    }
};
