<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('title');
            $table->text('description');
            $table->string('genre')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('trailer_path')->nullable();
            $table->enum('status', ['published', 'hidden'])->default('hidden');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
