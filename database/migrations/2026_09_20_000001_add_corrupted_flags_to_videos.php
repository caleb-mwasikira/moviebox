<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['movies', 'episodes'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->boolean('is_corrupted')->default(false);
                $t->timestamp('corrupted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['movies', 'episodes'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['is_corrupted', 'corrupted_at']);
            });
        }
    }
};
