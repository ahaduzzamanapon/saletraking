<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('work_started_at')->nullable()->after('last_seen_at');
            $table->timestamp('work_ended_at')->nullable()->after('work_started_at');
            $table->boolean('is_working')->default(false)->after('work_ended_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['work_started_at', 'work_ended_at', 'is_working']);
        });
    }
};
