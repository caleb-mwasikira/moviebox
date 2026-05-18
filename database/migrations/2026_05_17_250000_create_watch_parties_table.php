<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('watchable');
            $table->string('token', 32)->unique();
            $table->enum('status', ['waiting', 'live', 'ended'])->default('waiting');
            $table->boolean('is_playing')->default(false);
            $table->float('playback_position')->default(0);
            $table->timestamp('playback_updated_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_parties');
    }
};
