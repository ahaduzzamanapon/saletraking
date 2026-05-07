<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['daily', 'monthly']);
            $table->date('period_date'); // for daily: the date; for monthly: first day of month
            $table->unsignedInteger('visits_target')->default(0);
            $table->decimal('sales_target', 14, 2)->default(0);
            $table->unsignedInteger('visits_achieved')->default(0);
            $table->decimal('sales_achieved', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'type', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
