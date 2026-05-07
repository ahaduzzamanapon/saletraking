<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesperson_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();
            $table->decimal('checkin_lat', 10, 7)->nullable();
            $table->decimal('checkin_lng', 10, 7)->nullable();
            $table->enum('checkin_method', ['auto', 'manual'])->default('auto');
            $table->enum('status', ['in_progress', 'completed', 'missed'])->default('in_progress');
            $table->enum('outcome', ['order_placed', 'follow_up', 'not_interested', 'demo_requested', 'other'])->nullable();
            $table->unsignedTinyInteger('rating')->nullable(); // 1–5
            $table->decimal('order_value', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('contact_met')->nullable();
            $table->boolean('feedback_submitted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
