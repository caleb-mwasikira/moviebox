<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per browser-driven upload that is still sending bytes for a movie, series or episode.
        Schema::create('video_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('uploadable_type');
            $table->unsignedBigInteger('uploadable_id');
            $table->string('kind', 16); // video | trailer
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('received')->default(0);
            $table->timestamps();

            $table->index(['uploadable_type', 'uploadable_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_uploads');
    }
};
