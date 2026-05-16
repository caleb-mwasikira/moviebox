<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('invited_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('invite_email')->nullable()->after('pin');
            $table->string('invite_token')->nullable()->index()->after('invite_email');
            $table->string('status', 20)->default('active')->after('invite_token');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['invited_user_id']);
            $table->dropColumn(['invited_user_id', 'invite_email', 'invite_token', 'status']);
        });
    }
};
