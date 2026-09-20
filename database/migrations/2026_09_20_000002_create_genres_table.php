<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->timestamps();
        });

        // Start with the built-in list plus any genre already used by existing titles.
        $genres = collect([
            'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy',
            'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western',
        ])
            ->merge(DB::table('movies')->whereNotNull('genre')->distinct()->pluck('genre'))
            ->merge(DB::table('series')->whereNotNull('genre')->distinct()->pluck('genre'))
            ->map(fn ($g) => trim($g))
            ->filter()
            ->unique(fn ($g) => mb_strtolower($g))
            ->values();

        $now = now();
        DB::table('genres')->insert($genres->map(fn ($g) => ['name' => $g, 'created_at' => $now, 'updated_at' => $now])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
