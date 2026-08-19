<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('role')->nullable()->after('log_name');
            $table->string('ip_address', 45)->nullable()->after('properties');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->index(['causer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['causer_id', 'created_at']);
            $table->dropColumn(['role', 'ip_address', 'user_agent']);
        });
    }
};
