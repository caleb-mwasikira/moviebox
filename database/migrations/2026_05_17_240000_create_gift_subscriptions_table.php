<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subscription_package_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('token', 64)->unique();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'claimed'])->default('pending');
            $table->foreignId('claimed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_subscriptions');
    }
};
