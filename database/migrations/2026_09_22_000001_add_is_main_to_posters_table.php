<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('sort_order');
        });

        // Artwork is now one list of posters with one marked as main. Existing thumbnails become
        // posters (the `thumbnail` column stays as a copy of the main poster's path for fast lookups).
        foreach ([['movies', 'movie'], ['series', 'series']] as [$table, $type]) {
            DB::table($table)->whereNotNull('thumbnail')->orderBy('id')->each(function ($row) use ($type) {
                $posters = DB::table('posters')->where('posterable_type', $type)->where('posterable_id', $row->id);
                $existing = (clone $posters)->where('path', $row->thumbnail)->first();

                if (! $existing) {
                    $id = DB::table('posters')->insertGetId([
                        'posterable_type' => $type, 'posterable_id' => $row->id, 'path' => $row->thumbnail,
                        'sort_order' => -1, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                } else {
                    $id = $existing->id;
                }

                DB::table('posters')->where('id', $id)->update(['is_main' => true]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->dropColumn('is_main');
        });
    }
};
