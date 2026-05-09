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
        Schema::table('movies', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('status');
        });
        Schema::table('series', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('featured');
        });
        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn('featured');
        });
    }
};
