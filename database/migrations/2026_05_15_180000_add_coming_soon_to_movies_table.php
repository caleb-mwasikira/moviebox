<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->boolean('coming_soon')->default(false)->after('featured');
            $table->string('video_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('coming_soon');
            $table->string('video_path')->nullable(false)->change();
        });
    }
};
