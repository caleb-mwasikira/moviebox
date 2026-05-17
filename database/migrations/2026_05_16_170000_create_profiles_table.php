<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('avatar_color', 20)->default('#6366f1');
            $table->boolean('is_kids')->default(false);
            $table->string('pin')->nullable();
            $table->string('invite_email')->nullable();
            $table->string('invite_token')->nullable()->index();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
