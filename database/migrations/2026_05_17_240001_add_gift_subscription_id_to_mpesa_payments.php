<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_payments', function (Blueprint $table) {
            $table->foreignId('gift_subscription_id')
                ->nullable()
                ->after('subscription_package_id')
                ->constrained('gift_subscriptions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_payments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\GiftSubscription::class);
            $table->dropColumn('gift_subscription_id');
        });
    }
};
