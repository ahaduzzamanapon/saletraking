<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'manager', 'salesperson', 'analyst'])->default('salesperson')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('employee_id', 50)->nullable()->unique()->after('phone');
            $table->unsignedBigInteger('territory_id')->nullable()->after('employee_id');
            $table->string('fcm_token')->nullable()->after('territory_id');
            $table->string('avatar')->nullable()->after('fcm_token');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_seen_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'employee_id', 'territory_id', 'fcm_token', 'avatar', 'is_active', 'last_seen_at']);
        });
    }
};
