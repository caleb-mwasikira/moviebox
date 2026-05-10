<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('streaming_path')->nullable()->after('video_path');
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->string('streaming_path')->nullable()->after('video_path');
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('streaming_path');
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn('streaming_path');
        });
    }
};
