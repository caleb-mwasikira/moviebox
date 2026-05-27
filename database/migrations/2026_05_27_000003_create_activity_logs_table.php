<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name');
            $table->string('action');        // created | updated | deleted | restored | force_deleted
            $table->string('subject_type');  // Movie | Series | Episode
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_title');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
